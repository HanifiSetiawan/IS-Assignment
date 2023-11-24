<?php

namespace App\Http\Controllers;

use App\Mail\SendKey;
use App\Models\DataRequest;
use App\Models\User;
use Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Mail;

use App\Services\DecryptRequests;
use App\Services\EncryptRequests;

class HomeController extends Controller
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
        $user = Auth::user();

        $sentDataRequests = DataRequest::where('from', '=', $user->email)->get();
        $incomingDataRequests = DataRequest::where('to','=', $user->email)->get();

        return view('home', ['user' => $user->name, 'sent' => $sentDataRequests, 'incoming' => $incomingDataRequests]);
    }

    public function incoming(Request $request) {
        
        $validator = Validator::make($request->all(), [
            'state' => ['required', Rule::in(['accepted', 'rejected'])],
            'from' => ['required', 'email'],
            'to' => ['required', 'email']
        ]);

        if($validator->fails()) {
            return back()->withErrors($validator);
        }

        $validated = $validator->validated();

        
        $data = DataRequest::where('from','=', $validated['from'])->where('to','=', $validated['to'])->first();

        $data->state = $validated['state'];
        $data->save();

        return back()->with('success','Request responded successfully');
    }

    public function send(Request $request) {
        $validator = Validator::make($request->all(), [
            'state' => ['required', Rule::in(['email'])],
            'from' => ['required', 'email'],
            'to' => ['required', 'email']
        ]);

        if($validator->fails()) {
            return back()->withErrors($validator);
        }

        $user = Auth::user();

        $validated = $validator->validated();

        $respondeeExists = User::where('email','=', $validated['to'])->exists();

        if(!$respondeeExists) {
            return back()->with('error',"User doesn't exist");
        }

        $respondee = User::where('email','=', $validated['from'])->first();
        $public = $respondee->getUserKey('pub');

        
        $decryptor = function ($data, $key) {
            return $this->decryptRequests->decrypt($data, $key);
        };

        $app_key = config('app.key');

        $dec_public = $decryptor($public, $app_key);
        $dec_public = openssl_pkey_get_public($dec_public);
        $user_symkey = $user->getUserKey('sym');

        openssl_public_encrypt($user_symkey, $encryptedData, $dec_public);
        

        Mail::to($validated['from'])->send(new SendKey($validated['to'], $encryptedData));


        return back()->with('success','Email sent successfully');
    }
}
