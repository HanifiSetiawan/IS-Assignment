@extends('layouts.app')

@section('content')
<div class="container">
    <form method="POST" action="{{ route('data-pribadi.store') }}" enctype="multipart/form-data">
        @csrf

        <label for="nama">Nama:</label>
        <input type="text" name="nama" id="nama" required>

        <label for="nomor_telepon">Nomor Telepon:</label>
        <input type="text" name="nomor_telepon" id="nomor_telepon" required>

        <label for="foto_ktp">Foto KTP:</label>
        <input type="file" name="foto_ktp" id="foto_ktp" accept="image/*">

        <label for="file_pdf">File PDF:</label>
        <input type="file" name="file_pdf" id="file_pdf" accept=".pdf">

        <label for="file_doc">File DOC:</label>
        <input type="file" name="file_doc" id="file_doc" accept=".doc, .docx">

        <label for="file_xls">File XLS:</label>
        <input type="file" name="file_xls" id="file_xls" accept=".xls, .xlsx">

        <label for="video">Video (max 25MB):</label>
        <input type="file" name="video" id="video" accept="video/*" max-size="25mb">

        <button type="submit">Simpan</button>
    </form>
</div>
@endsection
