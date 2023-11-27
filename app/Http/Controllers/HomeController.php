<?php

namespace App\Http\Controllers;

use App\Mail\SendKey;
use App\Models\DataRequest;
use App\Models\User;
use App\Models\Orang;

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

        $decryptor = function ($data, $key) {
            return $this->decryptRequests->decrypt($data, $key);
        };

        $orangs = $user->getDecryptedOrangs($decryptor);

        return view('home', ['user' => $user->name, 'sent' => $sentDataRequests, 'incoming' => $incomingDataRequests, 'orangs' => $orangs]);
    }

    public function incoming(Request $request) {
        
        $validator = Validator::make($request->all(), [
            'state' => ['required', Rule::in(['accepted', 'rejected', 'cancelled'])],
            'from' => ['required', 'email'],
            'to' => ['required', 'email']
        ]);

        if($validator->fails()) {
            return back()->withErrors($validator);
        }

        $validated = $validator->validated();
        $data = DataRequest::where('from','=', $validated['from'])->where('to','=', $validated['to'])->first();
        if(!$data->exists()) return redirect()->back()->with('error',"Data doesn't exist");

        if($validated['state'] == 'cancelled') {
            $data->delete();
            return redirect()->back()->with('success', 'Record deleted successfully');
        }

        $data->state = $validated['state'];
        $data->save();

        return back()->with('success','Request responded successfully');
    }

    public function send(Request $request) {
        $validator = Validator::make($request->all(), [
            'state' => ['required', Rule::in(['email'])],
            'from' => ['required', 'email'],
            'to' => ['required', 'email'],
            'orang' => ['required', 'exists:App\Models\User,id']
        ]);

        if($validator->fails()) {
            return back()->withErrors($validator);
        }
        
        $user = Auth::user();
        
        if($user) {
            $validated = $validator->validated();

            $respondeeExists = User::where('email','=', $validated['from'])->exists();

            if(!$respondeeExists) {
                return back()->with('error',"User doesn't exist to send the data ");
            }

            $decryptor = function ($data, $key) {
                return $this->decryptRequests->decrypt($data, $key);
            };
            $respondee = User::where('email','=', $validated['from'])->first();

            try {
                $dec_public = $respondee->getAsymmetricKey($decryptor, 'pub');
            } catch (\Throwable $th) {
                return back()->withErrors(['error' => 'Getting public key has failed']);
            }
            
            if(empty($dec_public)) return redirect()->back()->with('error','Public key decryption has failed');
            
            
            
            $orang = Orang::find($validated['orang']);
            $user_symkey = $orang->key()->first()->key;

            try {
                $encryptedData = $dec_public->encrypt($user_symkey);
            } catch (\Throwable $th) {
                return redirect()->back()->with('error','Encrypting symmetric key has failed');
            }
            
            Mail::to($validated['from'])->send(new SendKey($validated['to'], $encryptedData));


            return back()->with('success','Email sent successfully');
        }
        return redirect('login')->with('error','User not authenticated');
    }
}
