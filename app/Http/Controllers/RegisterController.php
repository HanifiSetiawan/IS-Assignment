<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Services\EncryptRequests;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rules\Password;

class RegisterController extends Controller
{
    protected EncryptRequests $encryptRequests;

    public function __construct(EncryptRequests $encryptRequests) {
        $this->encryptRequests = $encryptRequests;
        $encAlgo = config('app.picked_cipher');
    }
    public function index() {
        return view('register');
    }

    public function register(Request $request) {
        $validator = Validator::make($request->all(), [
            'name' => ['required', 'ascii'],
            'email' => ['required', 'email', 'unique:users,name'],
            'password' => ['required', Password::min(8)
                                            ->letters()
                                            ->mixedCase()
                                            ->numbers()]
        ]);

        if($validator->stopOnFirstFailure()->fails()) {
            return back()->withErrors($validator);
        }

        $validated = $validator->validated();

        $key_of_key = config('app.key');

        $user = new User;
        $user->name = $validated['name'];
        $user->email = $validated['email'];
        $user->password = bcrypt($validated['password']);
        $user->save();


        return view('login');

    }
}
