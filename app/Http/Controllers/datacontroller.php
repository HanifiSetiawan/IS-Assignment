<?php

namespace App\Http\Controllers;
use App\Models\Orang;
use App\Services\DecryptRequests;
use App\Services\EncryptRequests;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Response;

class datacontroller extends Controller
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
    public function index(){

        $time_start = microtime(true);

        $decryptor = function ($data, $key) {
            return $this->decryptRequests->decrypt($data, $key);
        };

        $user = Auth::user();

        if($user) {
            $app_key = config('app.key');

            $key = $decryptor($user->getUserKey('sym'), $app_key);
            if(empty($key)) return redirect()->back()->with('error','Symmetrical Key Decryption has failed');


            $exist = $user->orangs()->exists();
            if(!$exist) return view('show'); 

            $orangs = $user->orangs()->get();
            foreach ($orangs as $orang) {

                $pic = Storage::get($orang->foto_ktp);


                $orang->nama = $decryptor($orang->nama, $key);
                $orang->nomor_telepon = $decryptor($orang->nomor_telepon, $key);

                $foto_ktp_dec = $decryptor($pic, $key);
                $orang->foto_ktp = $foto_ktp_dec;    
        }

            $time_finish = microtime(true);

            $difference = $time_finish - $time_start;
            return view('show', ['orangs' => $orangs, 'time' => $difference, 'route' => 'show.download']);
        }
        return redirect()->back()->with('error','User invalid');
    }

    public function download($orang_id, $ext, $file) {

        $decryptor = function ($data, $key) {
            return $this->decryptRequests->decrypt($data, $key);
        };

        $user = Auth::user();
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
