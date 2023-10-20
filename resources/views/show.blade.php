<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Data</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.1/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-4bw+/aepP/YC94hEpVNVgiZdgIC5+VKNBQNGCHeKRQN+PtmoHDEXuppvnDJzQIu9" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/css/bootstrap.min.css">
    <style>
        .table-wrap {
            word-wrap: break-word;
        }

        .foto {
            width: 100px;
            height: auto;
        }
    </style>
</head>
<body>
    @include('navbar')
<div class="container">
        <h1>Data</h1>
        <p>Time decrypting Name & Phone_num : {{$time}} microseconds</p>
        <table class="table table-bordered table-wrap">
            <thead>
                <tr>
                    <th>Nama</th>
                    <th>Nomor Telepon</th>
                    <th>Foto KTP</th>
                    <th>Dokumen</th>
                    <th>Video</th>
                </tr>
            </thead>
            <tbody>
                @foreach($orangs as $orang)
                <tr>
                    <td>{{ $orang->nama }}</td>
                    <td>{{ $orang->nomor_telepon }}</td>
                    <td>
                        <img class="foto" src="data:image/png;base64,{{ $orang->foto_ktp }}" alt="Foto KTP">
                    </td>
                    <td>
                        <a class="btn btn-primary" href="{{ route('document', ['orang_id' => $orang->id]) }}">Download dokumen</a>

                    </td>
                    <td>
                        <a class="btn btn-primary" href="{{ route('video', ['orang_id' => $orang->id]) }}" download>Download video</a>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</body>
</html>