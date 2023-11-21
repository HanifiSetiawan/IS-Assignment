<?php

namespace App\Http\Controllers;

use App\Models\Key;
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
        $this->encryptRequests->setAlgorithm($encAlgo);
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

        $encryptor = function ($data, $key) {
            return $this->encryptRequests->encrypt_with_key($data, $key);
        };

        $validated = $validator->validated();

        $app_key = config('app.key');

        
        $user_key = random_bytes(32);

        $user = new User;
        $user->name = $validated['name'];
        $user->email = $validated['email'];
        $user->password = bcrypt($validated['password']);
        $user->save();

        $key_user = new Key;
        $key_user->key = $encryptor($user_key, $app_key);
        $key_user->user_id = $user->id;
        $key_user->type = 'sym';
        $key_user->save();

        return view('login');

    }
}
