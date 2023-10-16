<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rules\Password;

class LoginController extends Controller
{
    public function index() {
        return view('login');
    }

    public function login(Request $request) {
        $validator = $request->validate([
            'email' => 'required|email',
            'password' => 'required'
        ]);
            
        
        if(Auth::attempt($validator)) {
            $request->session()->regenerate();
            return redirect()->intended('/form');
        }

        return back()->withErrors([
            'email' => 'The provided credentials do not match our records.',
        ])->onlyInput('email');

    }
    
}
