<?php

namespace App\Http\Controllers;

use App\Models\Key;
use App\Services\EncryptRequests;
use Illuminate\Http\Request;
use App\Models\Orang;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class OrangController extends Controller
{
    protected EncryptRequests $encryptRequests;

    public function __construct(EncryptRequests $encryptRequests) {
        $this->encryptRequests = $encryptRequests;
    }

    public function index()
    {
        return view('form');
    }

    public function simpanData(Request $request)
    {
        
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

        $enctype = 'aes-256-cbc';

        $nama = $this->encryptRequests->encrypt($request->input('nama'), $enctype);
        $no_telp = $this->encryptRequests->encrypt($request->input('nomor_telepon'), $enctype);

        $foto = $request->file('foto_ktp');
        $dokumen = $request->file('dokumen');
        $video = $request->file('video');

        //base64 endokumen_enc$foto_enc = $this->encryptRequests->encrypt(base64_encode($foto->get()),$enctype);

        $dokumen_enc = $this->encryptRequests->encrypt(base64_encode($dokumen->get()), $enctype);
        $foto_enc = $this->encryptRequests->encrypt(base64_encode($foto->get()), $enctype);
        $video_enc = $this->encryptRequests->encrypt(base64_encode($video->get()),$enctype);
        
        $filefoto = "public/foto_ktp/" . Str::random();
        $filedokumen = "public/dokumen/" . Str::random();
        $filevideo = "public/video/" . Str::random();

        Storage::put($filefoto, $foto_enc['enc']);
        Storage::put($filedokumen, $dokumen_enc['enc']);
        Storage::put($filevideo, $video_enc['enc']);

        $orang = new Orang;
        $orang->nama = $nama['enc'];
        $orang->nomor_telepon = $no_telp['enc'];
        $orang->foto_ktp = $filefoto;
        $orang->ext_foto = $foto->getClientOriginalExtension();
        $orang->dokumen = $filedokumen;
        $orang->ext_doc = $dokumen->getClientOriginalExtension();
        $orang->video = $filevideo;
        $orang->ext_vid = $video->getClientOriginalExtension();
        $orang->user_id = auth()->id();
        $orang->save();
        
        
        
        $orang_id = $orang->id;

        $key_nama = new Key;
            $key_nama->purpose = 'nama';
            $key_nama->iv = $nama['iv'];
            $key_nama->key = $nama['key'];
            $key_nama->user_id = auth()->id();
            $key_nama->orang_id = $orang_id;
        $key_nama->save();
        
        $key_notelp = new Key;
            $key_notelp->purpose = 'nomor_telepon';
            $key_notelp->iv = $no_telp['iv'];
            $key_notelp->key = $no_telp['key'];
            $key_notelp->user_id = auth()->id();
            $key_notelp->orang_id = $orang_id;
        $key_notelp->save();

        $key_foto = new Key;
            $key_foto->purpose = 'foto_ktp';
            $key_foto->iv = $foto_enc['iv'];
            $key_foto->key = $foto_enc['key'];
            $key_foto->user_id = auth()->id();
            $key_foto->orang_id = $orang_id;
        $key_foto->save();

        $key_dokumen = new Key;
            $key_dokumen->purpose = 'dokumen';
            $key_dokumen->iv = $dokumen_enc['iv'];
            $key_dokumen->key = $dokumen_enc['key'];
            $key_dokumen->user_id = auth()->id();
            $key_dokumen->orang_id = $orang_id;
        $key_dokumen->save();

        $key_video = new Key;
            $key_video->purpose = 'video';
            $key_video->iv = $video_enc['iv'];
            $key_video->key = $video_enc['key'];
            $key_video->user_id = auth()->id();
            $key_video->orang_id = $orang_id;
        $key_video->save();

        return view('submit');
    }

}

