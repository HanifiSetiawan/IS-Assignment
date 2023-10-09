<!-- resources/views/form.blade.php -->

<!DOCTYPE html>
<html>
<head>
    <title>Form Pengumpulan Data</title>
    <link rel="stylesheet" href="{{ asset('css/form.css') }}">
</head>
<body>
    

    <h1>Form Pengumpulan Data</h1>
    <form action="{{ route('simpan-data') }}" method="POST" enctype="multipart/form-data">
        @csrf
        <div class="form-group">
            <label for="nama">Nama:</label>
            <input type="text" name="nama" class="form-control">
        </div>
        <div class="form-group">
            <label for="nomor_telepon">Nomor Telepon:</label>
            <input type="text" name="nomor_telepon" class="form-control">
        </div>
        <div class="form-group">
            <label for="foto_ktp">Foto KTP:</label>
            <input type="file" name="foto_ktp" accept="image/*" class="form-control">
        </div>
        <div class="form-group">
            <label for="dokumen">Dokumen:</label>
            <input type="file" name="dokumen" accept=".pdf,.doc,.docx,.xls,.xlsx" class="form-control">
        </div>
        <div class="form-group">
            <label for="video">Video (max 25MB):</label>
            <input type="file" name="video" accept="video/*" class="form-control" max-size="25000">
        </div>
        <button type="submit" id="submitbutton" class="btn btn-primary">Simpan</button>
    </form>
</body>
</html>
