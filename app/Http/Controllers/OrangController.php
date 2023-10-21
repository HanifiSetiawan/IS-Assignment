<?php

namespace App\Http\Controllers;

use App\Models\Key;
use App\Services\DecryptRequests;
use App\Services\EncryptRequests;
use Illuminate\Http\Request;
use App\Models\Orang;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class OrangController extends Controller
{
    protected EncryptRequests $encryptRequests;
    protected DecryptRequests $decryptRequests;


    public function __construct(EncryptRequests $encryptRequests, DecryptRequests $decryptRequests) {
        $this->encryptRequests = $encryptRequests;
        $this->decryptRequests = $decryptRequests;
        $encAlgo = config('app.picked_cipher');
        $this->encryptRequests->setAlgorithm($encAlgo);
        $this->decryptRequests->setAlgorithm($encAlgo);
    }

    public function index()
    {
        return view('form');
    }

    public function simpanData(Request $request)
    {
        
        $time_start = microtime(true);
        // Validasi data yang dikirimkan oleh pengguna
        $validator = Validator::make($request->all(),
        [
            'nama' => 'required|string',
            'nomor_telepon' => 'required|string',
            'foto_ktp' => 'required|image',
            'dokumen' => 'required|mimes:pdf,doc,docx,xls,xlsx',
            'video' => 'required|file|max:25000|mimetypes:video/*',
        ]
        );


        if($validator->fails()) {
            return redirect()->back()->withErrors($validator);
        }

        $encryptor = function ($data, $key) {
            return $this->encryptRequests->encrypt_with_key($data, $key);
        };
        $decryptor = function ($data, $key) {
            return $this->decryptRequests->decrypt($data, $key);
        };

        $user = Auth::user();
        $app_key = config('app.key');
        $key = $decryptor($user->keys['key'], $app_key);


        $nama = $encryptor($request->input('nama'), $key);
        $no_telp = $encryptor($request->input('nomor_telepon'), $key);

        $foto = $request->file('foto_ktp');
        $dokumen = $request->file('dokumen');
        $video = $request->file('video');



        $dokumen_enc = $encryptor(base64_encode($dokumen->get()), $key);
        $foto_enc = $encryptor(base64_encode($foto->get()), $key);
        $video_enc = $encryptor(base64_encode($video->get()), $key);
        
        $filefoto = "public/foto_ktp/" . Str::random();
        $filedokumen = "public/dokumen/" . Str::random();
        $filevideo = "public/video/" . Str::random();

        Storage::put($filefoto, $foto_enc);
        Storage::put($filedokumen, $dokumen_enc);
        Storage::put($filevideo, $video_enc);

        $orang = new Orang;
        $orang->nama = $nama;
        $orang->nomor_telepon = $no_telp;
        $orang->foto_ktp = $encryptor($filefoto, $key);
        $orang->ext_foto = $foto->getClientOriginalExtension();
        $orang->dokumen = $encryptor($filedokumen, $key);
        $orang->ext_doc = $dokumen->getClientOriginalExtension();
        $orang->video = $encryptor($filevideo, $key);
        $orang->ext_vid = $video->getClientOriginalExtension();
        $orang->user_id = $user->id;
        $orang->save();
        

        $time_finish = microtime(true);

        $difference = $time_finish - $time_start;

        return view('submit', ['time' => $difference]);
    }

}

