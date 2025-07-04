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
            'username' => ['required'],
            'password' => ['required'],
            
        ]);
        $remember = $request->has('remember');
 
        if (Auth::attempt($credentials, $remember)) {
            $request->session()->regenerate();

            $data = Auth::user(); // gets the whole information of the user
            $role = $data -> role; // this gets the role of the user from the table

            if($remember){
                Cookie::queue('last_username', $credentials['username']);
                Cookie::queue('last_password', $credentials['password']);
                Cookie::queue('remember_me', true, 60 * 24 * 30);
            }else{
                Cookie::queue('remember_me', false, 60 * 24 * 30);
            }


            switch($role){
                
                case 'admin':
                     return redirect() -> route('dashboard.admin');
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
            'username' => 'The provided credentials do not match our records.',
            'password' => 'The provided credentials do not match our records.',
        ])->onlyInput('username','password');
    }

    public function logout(Request $request): RedirectResponse
    {
        Auth::logout();
    
        $request->session()->invalidate();
    
        $request->session()->regenerateToken();
    
        return redirect('/');
    }
}
