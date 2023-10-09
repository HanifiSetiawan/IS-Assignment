<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rules\Password;

class LoginController extends Controller
{
    public function index() {
        return view('login');
    }

    public function Login(Request $request) {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'password' => Password::min(8)
                            ->mixedCase()
                            ->numbers()
        ]);
        
        if($validator->fails()) {
            return redirect('login')->withErrors($validator);
        }


        //check email and password with database
        
        //go to home page
    }
}
