<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;


class FileVerificationController extends Controller
{
    public function index() {
        return view('verify-file');
    }


    public function verify(Request $request) {
        $validator = Validator::make($request->all(),[
            'file' => 'required|mimes:pdf'
        ]);

        if($validator->fails()) {
            return redirect()->back()->withErrors($validator);
        }

        $validated = $validator->validated();

        $file = $validated['file'];

        dd($file);

        return 'testing';
    }
}
