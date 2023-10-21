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
            $key = $decryptor($user->keys['key'], $app_key);

            $orangs = $user->orangs()->get();
            foreach ($orangs as $orang) {

                $foto_ktp_filepath = $decryptor($orang->foto_ktp, $key);

                $pic = Storage::get($foto_ktp_filepath);


                $orang->nama = $decryptor($orang->nama, $key);
                $orang->nomor_telepon = $decryptor($orang->nomor_telepon, $key);

                $foto_ktp_dec = $decryptor($pic, $key);
                $orang->foto_ktp = $foto_ktp_dec;

                
            }

            $time_finish = microtime(true);

            $difference = $time_finish - $time_start;
            return view('show', ['orangs' => $orangs, 'time' => $difference]);
        }
    }

    public function download($orang_id, $ext, $file) {

        $decryptor = function ($data, $key) {
            return $this->decryptRequests->decrypt($data, $key);
        };

        $user = Auth::user();
        $app_key = config('app.key');
        $key = $decryptor($user->keys['key'], $app_key);

        $file = $decryptor($file, $key);

        $orang = Orang::find($orang_id);
        $doc = Storage::get($file);

        $dok_dec = $decryptor($doc, $key);

        $filepath = 'file' . '.' . $ext;
        Storage::put($filepath, base64_decode($dok_dec));

        $response = response()->download(Storage::path($filepath))->deleteFileAfterSend(true);


        return $response;
    }
}
