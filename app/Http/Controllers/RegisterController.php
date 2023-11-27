<?php

namespace App\Http\Controllers;

use App\Models\Key;
use App\Models\User;
use App\Services\EncryptRequests;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rules\Password;
use phpseclib3\Crypt\RSA;
use Auth;

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
        $asym = $this->createKeys();


        $user = new User;
        $user->name = $validated['name'];
        $user->email = $validated['email'];
        $user->password = bcrypt($validated['password']);
        $user->save();

        //Deleted symmetric key


        $key_priv = new Key;
        $key_priv->key = $encryptor($asym['private'], $app_key);
        $key_priv->user_id = $user->id;
        $key_priv->type = 'priv';
        if(empty($key_priv->key))
            return back()->withErrors(['encfail' => 'key encryption process (private) has failed']);
        $key_priv->save();


        $key_pub = new Key;
        $key_pub->key = $encryptor($asym['public'], $app_key);
        $key_pub->user_id = $user->id;
        $key_pub->type = 'pub';
        if(empty($key_pub->key))
        return back()->withErrors(['encfail' => 'key encryption process (public) has failed']);
        $key_pub->save();

        if(Auth::attempt([
            'email' => $validated['email'],
            'password' => $validated['password']
        ])) {
            $request->session()->regenerate();
            return redirect()->intended('home');
        }

        return redirect('login')->withErrors([
            'email' => 'The provided credentials do not match our records.',
        ])->onlyInput('email');

    }

    private function createKeys() {
        $privateKey = RSA::createKey();
        $publicKey = $privateKey->getPublicKey();

        return ['private' => $privateKey->toString('PKCS8'), 'public' => $publicKey->toString('PKCS8')];
    }
}
