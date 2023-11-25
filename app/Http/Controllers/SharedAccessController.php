<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Auth;
use App\Services\DecryptRequests;
use App\Services\EncryptRequests;

class SharedAccessController extends Controller
{

    protected $decryptRequests;
    protected $encryptRequests;

    public function __construct(DecryptRequests $decryptRequests, EncryptRequests $encryptRequests) {
        $encAlgo = config('app.picked_cipher');
        $this->decryptRequests = $decryptRequests;
        $this->decryptRequests->setAlgorithm($encAlgo);
        $this->encryptRequests = $encryptRequests;
        $this->encryptRequests->setAlgorithm($encAlgo);
    }
    public function index() {
        return view("share-access");
    }

    public function show(Request $request) {

        
        $validator = Validator::make($request->all(), [
            'key' => ['required','string']
        ]);
        
        if($validator->stopOnFirstFailure()->fails()) {
            return back()->withErrors($validator);
        }
        
        $validated = $validator->validated();
        $user = Auth::user();
        $key = base64_decode($validated['key']);


        $decryptor = function ($data, $key) {
            return $this->decryptRequests->decrypt($data, $key);
        };

        $app_key = config('app.key');
        $usr_priv = $decryptor($user->getUserKey('priv'), $app_key);
        $usr_priv = openssl_pkey_get_private($usr_priv);

        openssl_private_decrypt($key, $decryptedData, $usr_priv);

        if(empty($decryptedData)) {
            return back()->withErrors(['decfail' => 'Decryption has failed (private key)']);
        }

        dd($usr_priv);


    }
}
