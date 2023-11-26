<?php

namespace App\Http\Controllers;

use App\Models\Key;
use App\Models\User;
use App\Services\EncryptRequests;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rules\Password;
use Spatie\Crypto\Rsa\KeyPair;

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


        $user = new User;
        $user->name = $validated['name'];
        $user->email = $validated['email'];
        $user->password = bcrypt($validated['password']);
        $user->save();

        //Deleted symmetric key

        $asym = $this->createKeys();

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


        return view('login');

    }

    private function createKeys() {
        $config = [
            'private_key_bits' => 2048,
            'private_key_type' => OPENSSL_KEYTYPE_RSA,
        ];

        $pkey = openssl_pkey_new($config);

        if ($pkey == false) {
            $config['config'] = '/opt/homebrew/etc/openssl@3/openssl.cnf';
        }


        $pkey = openssl_pkey_new($config);
        openssl_pkey_export($pkey, $privateKey, NULL, $config);

        $publicKey = openssl_pkey_get_details($pkey);
        $publicKey = $publicKey["key"];

        return ['private' => $privateKey, 'public' => $publicKey];
    }
}
