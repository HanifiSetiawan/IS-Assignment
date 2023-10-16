<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Orang; // Sesuaikan dengan nama model yang kamu gunakan
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class OrangController extends Controller
{
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

        // Simpan file foto KTP
        $fotoKtpPath = $request->file('foto_ktp')->store('public/foto_ktp');

        // Simpan file dokumen
        $dokumenPath = $request->file('dokumen')->store('public/dokumen');

        // Simpan file video
        $videoPath = $request->file('video')->store('public/video');

        // Simpan data ke dalam database
        $orang = new Orang;
        $orang->nama = $request->input('nama');
        $orang->nomor_telepon = $request->input('nomor_telepon');
        $orang->foto_ktp = $fotoKtpPath;
        $orang->dokumen = $dokumenPath;
        $orang->video = $videoPath;
        $orang->save();

        return view('submit');
    }

}

