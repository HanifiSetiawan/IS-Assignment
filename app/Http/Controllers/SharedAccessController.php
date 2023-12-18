<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Auth;
use App\Services\DecryptRequests;
use App\Services\EncryptRequests;
use App\Models\Key;
use App\Models\User;
use App\Models\Orang;
use App\Services\SignDocument;


use Illuminate\Support\Facades\Storage;

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

        $time_start = microtime(true);

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

        try {
            $usr_priv = $user->getAsymmetricKey($decryptor, 'priv');
        } catch (\Throwable $th) {
            return back()->withErrors(['decfail' => 'Getting private key has failed']);
        }
        
        try {
            $sym_key = $usr_priv->decrypt($key);
        } catch (\Throwable $th) {
            return back()->withErrors(['decfail' => 'Decryption has failed (symmetric key)']);
        }
        

        $s_keymodel = Key::where('key', $sym_key)->first();
        $s_userid = $s_keymodel->user_id;

        $s_user = User::find($s_userid);

        if($s_user) {

            $app_key = config('app.key');

            $orangs = Orang::where('key_id', $s_keymodel->id)->get();
            foreach ($orangs as $orang) {
                $sym_key = $decryptor($sym_key, $app_key);
                $pic = Storage::get($orang->foto_ktp);
                $orang->nama = $decryptor($orang->nama, $sym_key);
                $orang->nomor_telepon = $decryptor($orang->nomor_telepon, $sym_key);

                $foto_ktp_dec = $decryptor($pic, $sym_key);
                $orang->foto_ktp = $foto_ktp_dec;
            }

            $time_finish = microtime(true);

            $difference = $time_finish - $time_start;
            session(['check-access-route' => true]);

            return view('show', ['orangs' => $orangs, 'time' => $difference, 'route' => "shared.download"]);
        }
        return redirect()->back()->with('error','User invalid');
        
    }

    public function download($orang_id, $ext, $file) {

        $decryptor = function ($data, $key) {
            return $this->decryptRequests->decrypt($data, $key);
        };

        $user = Auth::user();

        if($user) {
            $orang = Orang::find($orang_id);
            $app_key = config('app.key');
            $key = $orang->key()->first();
            $key = $decryptor($key->key, $app_key);
            if(empty($key)) return redirect()->back()->with('error','Symmetrical Key Decryption has failed');
    
    
            $doc = Storage::get($file);
            $dok_dec = $decryptor($doc, $key);
            if(empty($dok_dec)) return redirect()->back()->with('error','File Decryption has failed');

            $signer = new SignDocument;
            $decoded = base64_decode($dok_dec);
            $decoded = $signer->sign($decryptor, $orang->user() ,$decoded);
    
            $filepath = 'file' . '.' . $ext;
            Storage::put($filepath, $decoded);
            $response = response()->download(Storage::path($filepath))->deleteFileAfterSend(true);
            return $response;
        }



        return redirect()->back()->with('error','User invalid');
    }
}
