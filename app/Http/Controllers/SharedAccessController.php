<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Auth;
use App\Services\DecryptRequests;
use App\Services\EncryptRequests;
use App\Models\Key;
use App\Models\User;
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

        $app_key = config('app.key');
        $usr_priv = $decryptor($user->getUserKey('priv'), $app_key);
        $usr_priv = openssl_pkey_get_private($usr_priv);

        openssl_private_decrypt($key, $sym_key, $usr_priv);

        if(empty($sym_key)) {
            return back()->withErrors(['decfail' => 'Decryption has failed (private key)']);
        }

        $s_userid = Key::where('key', $sym_key)->first()->user_id;

        $s_user = User::find($s_userid);

        if($s_user) {

            $key = $decryptor($sym_key, $app_key);
            if(empty($key)) return redirect()->back()->with('error','Symmetrical Key Decryption has failed');


            $exist = $s_user->orangs()->exists();
            if(!$exist) return view('show'); 

            $orangs = $s_user->orangs()->get();
            foreach ($orangs as $orang) {

                $pic = Storage::get($orang->foto_ktp);


                $orang->nama = $decryptor($orang->nama, $key);
                $orang->nomor_telepon = $decryptor($orang->nomor_telepon, $key);

                $foto_ktp_dec = $decryptor($pic, $key);
                $orang->foto_ktp = $foto_ktp_dec;

            }
            $time_finish = microtime(true);

            $difference = $time_finish - $time_start;
            session(['check-access-route' => true, 's_user' => $s_user]);

            return view('show', ['orangs' => $orangs, 'time' => $difference, 'route' => "shared.download"]);
        }
        return redirect()->back()->with('error','User invalid');
        
    }

    public function download($orang_id, $ext, $file) {

        $decryptor = function ($data, $key) {
            return $this->decryptRequests->decrypt($data, $key);
        };

        $user = session('s_user');
        $app_key = config('app.key');
        $key = $decryptor($user->getUserKey('sym'), $app_key);
        if(empty($key)) return redirect()->back()->with('error','Symmetrical Key Decryption has failed');


        $doc = Storage::get($file);
        $dok_dec = $decryptor($doc, $key);
        if(empty($dok_dec)) return redirect()->back()->with('error','File Decryption has failed');


        $filepath = 'file' . '.' . $ext;
        Storage::put($filepath, base64_decode($dok_dec));
        $response = response()->download(Storage::path($filepath))->deleteFileAfterSend(true);


        return $response;
    }
}
