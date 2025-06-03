<?php
 
namespace App\Http\Controllers;
 
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
 
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
 
        if (Auth::attempt($credentials)) {
            $request->session()->regenerate();

            $data = Auth::user(); // gets the whole information of the user
            $role = $data -> role; // this gets the role of the user from the table

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
