@extends('layouts.app')

@section('content')
<div class="container">
    <h1>Data Pribadi</h1>
    
    <h2>Nama: {{ $data->nama }}</h2>
    <p>Nomor Telepon: {{ $data->nomor_telepon }}</p>

    <!-- Tampilkan foto KTP jika tersedia -->
    @if ($data->foto_ktp)
        <img src="{{ asset('storage/foto_ktp/' . $data->foto_ktp) }}" alt="Foto KTP">
    @endif

    <!-- Tampilkan tautan ke file PDF jika tersedia -->
    @if ($data->file_pdf)
        <a href="{{ asset('storage/files/pdf/' . $data->file_pdf) }}" target="_blank">Lihat File PDF</a>
    @endif

    <!-- Tampilkan tautan ke file DOC jika tersedia -->
    @if ($data->file_doc)
        <a href="{{ asset('storage/files/doc/' . $data->file_doc) }}" target="_blank">Lihat File DOC</a>
    @endif

    <!-- Tampilkan tautan ke file XLS jika tersedia -->
    @if ($data->file_xls)
        <a href="{{ asset('storage/files/xls/' . $data->file_xls) }}" target="_blank">Lihat File XLS</a>
    @endif

    <!-- Tampilkan video jika tersedia -->
    @if ($data->video)
        <video width="320" height="240" controls>
            <source src="{{ asset('storage/videos/' . $data->video) }}" type="video/mp4">
            Your browser does not support the video tag.
        </video>
    @endif
</div>
@endsection
