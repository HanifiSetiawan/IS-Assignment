<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\DataPribadi;

class DataPribadiController extends Controller
{
    public function index()
    {
        $dataPribadi = DataPribadi::all();
        return view('data-pribadi.index', compact('dataPribadi'));
    }

    public function create()
    {
        return view('data-pribadi.create');
    }

    public function store(Request $request)
    {
        // Validasi input data
        $validatedData = $request->validate([
            'nama' => 'required|string|max:255',
            'nomor_telepon' => 'required|string|max:255',
            'foto_ktp' => 'image|mimes:jpeg,png,jpg,gif|max:2048',
            'file_pdf' => 'file|mimes:pdf|max:2048',
            'file_doc' => 'file|mimes:doc,docx|max:2048',
            'file_xls' => 'file|mimes:xls,xlsx|max:2048',
            'video' => 'file|mimetypes:video/*|max:25600', // Maksimum 25MB
        ]);

        // Simpan data ke dalam database
        $data = new DataPribadi();
        $data->nama = $validatedData['nama'];
        $data->nomor_telepon = $validatedData['nomor_telepon'];

        // Simpan file-file
        if ($request->hasFile('foto_ktp')) {
            $foto_ktp = $request->file('foto_ktp');
            $foto_ktp->store('public/foto_ktp');
            $data->foto_ktp = $foto_ktp->hashName();
        }

        if ($request->hasFile('file_pdf')) {
            $file_pdf = $request->file('file_pdf');
            $file_pdf->store('public/files/pdf');
            $data->file_pdf = $file_pdf->hashName();
        }

        if ($request->hasFile('file_doc')) {
            $file_doc = $request->file('file_doc');
            $file_doc->store('public/files/doc');
            $data->file_doc = $file_doc->hashName();
        }

        if ($request->hasFile('file_xls')) {
            $file_xls = $request->file('file_xls');
            $file_xls->store('public/files/xls');
            $data->file_xls = $file_xls->hashName();
        }

        if ($request->hasFile('video')) {
            $video = $request->file('video');
            $video->store('public/videos');
            $data->video = $video->hashName();
        }

        $data->save();

        return redirect()->route('data-pribadi.index')->with('success', 'Data pribadi berhasil disimpan.');
    }

    // Fungsi-fungsi lainnya (seperti edit, update, dan delete) bisa ditambahkan sesuai kebutuhan.
}
