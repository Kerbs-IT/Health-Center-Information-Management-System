<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;
use RealRashid\SweetAlert\Facades\Alert;

class forgotPassController extends Controller
{
    
    public function verify(Request $request){

        $data = $request -> validate([
            'email' => ['required', 'email']
        ]);
        $user = User::where('email', $request -> email) -> first();

        if($user){
            session([
                'reset_user_id' => $user->id,
                'recovery_email' => $request->email
            ]);
            return redirect()->route('forgot.pass.questions');
        }else{
            return redirect()->back()->with('error', "We couldn't verify your identity. Please try again.");
        }
    }

    public function verify_answer(Request $request){
        $user = User::find(session('reset_user_id'));

    

        if(($user -> recovery_question == $request -> recovery_question) && Hash::check($request -> recovery_answer, $user-> recovery_answer)){
            return redirect() -> route('forgot.pass.change');
        }else{
            return redirect() -> back() -> with('error', "Wrong answer. Please try again.");
        }
    }

    public function change_pass(Request $request){
        
        $data = $request -> validate([
            'password' => ['required','confirmed', Password::min(8)->letters()->mixedCase()->numbers()->symbols()],
        ]);

        $data['password'] = Hash::make($data['password']);
        $user = User::find(session('reset_user_id'));

        $user -> update($data);

        Alert::success('congrats','Password changed successfully. You can now log in.');
        return redirect()->route('forgot.pass.change')-> with('success', true);

    }
}
