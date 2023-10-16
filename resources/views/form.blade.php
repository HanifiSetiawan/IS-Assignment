<!-- resources/views/form.blade.php -->

<!DOCTYPE html>
<html>
<head>
    <title>Form Pengumpulan Data</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.1/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-4bw+/aepP/YC94hEpVNVgiZdgIC5+VKNBQNGCHeKRQN+PtmoHDEXuppvnDJzQIu9" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/css/bootstrap.min.css">
    <link rel="stylesheet" href="{{ asset('css/form.css') }}">
</head>
<body>
    <div class="title">
        <h1 class="text-black ">Form Pengumpulan Data</h1>
    </div>    
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
        <button type="submit" id="submitbutton" class="btn btn-primary">simpan</button>
    </form>
</body>
</html>
