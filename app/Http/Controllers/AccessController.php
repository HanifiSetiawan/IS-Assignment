<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class AccessController extends Controller
{
    public function index() {
        return view('access');
    }

    public function submit(Request $request) {
        $validator = Validator::make($request->all(), [
            'email' => ['required', 'email', 'exists:App\Models\User,email']
        ]);
        
        if($validator->fails()) {
            return redirect()->back()->withErrors($validator);
        }
    }

    
}
