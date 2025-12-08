<?php
 
namespace App\Http\Controllers;
 
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cookie;

class LoginController extends Controller
{
    /**
     * Handle an authentication attempt.
     */
    public function authenticate(Request $request): RedirectResponse
    {
        $credentials = $request->validate([
            'email' => ['required'],
            'password' => ['required'],
            
        ]);
        $remember = $request->has('remember');
        

        if (Auth::attempt($credentials, $remember)) {
            $request->session()->regenerate();

            $data = Auth::user();

            if (Auth::user()->status !== 'active') {
                $status = Auth::user()->status;
                Auth::logout(); // force logout if not active
                return back()->withErrors([
                    'email' => 'Your account is ' . $data->status  . '. Please wait for approval.',
                ])->onlyInput('email');
            }

             // gets the whole information of the user
            $role = $data -> role; // this gets the role of the user from the table

            if($remember){
                Cookie::queue('last_email', $credentials['email']);
                Cookie::queue('last_password', $credentials['password']);
                Cookie::queue('remember_me', true, 60 * 24 * 30);
            }else{
                Cookie::queue('remember_me', false, 60 * 24 * 30);
            }


            switch($role){
                
                case 'patient':
                     return redirect() -> route('dashboard.patient');
                case 'nurse':
                    
                    return redirect() -> route('dashboard.nurse');
                    break;
                case 'staff':
                    return redirect() -> route('dashboard.staff');
                default:
                    

            }
            
 
            // return redirect()->intended('dashboard');
        }
 
        return back()->withErrors([
            'email' => 'The provided credentials do not match our records.',
            'password' => 'The provided credentials do not match our records.',
        ])->onlyInput('email','password');
    }

    public function logout(Request $request): RedirectResponse
    {
        Auth::logout();
    
        $request->session()->invalidate();
    
        $request->session()->regenerateToken();
    
        return redirect('/');
    }
}
