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
        .alert {
            margin-left: 5vh;
            max-width: 50%;
        }
    </style>
</head>
<body>
    @include('navbar')
    @if(!empty($orangs))
    <div class="container">
        <h1>Data</h1>
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
                        <a class="btn btn-primary" href="{{ route( $route, ['orang_id' => $orang->id, 'ext' => $orang->ext_doc, 'file' => $orang->dokumen]) }}">Download dokumen</a>

                    </td>
                    <td>
                        <a class="btn btn-primary" href="{{ route( $route, ['orang_id' => $orang->id, 'ext' => $orang->ext_vid, 'file' => $orang->video]) }}">Download video</a>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
        <p>Time to get page : {{$time}} microseconds</p>
    </div>
    @else
    <div class="alert alert-danger" style="margin-top: 5vh;">
        No data found
    </div>
    @endif

    @if ($errors->any())
    <div class="alert alert-danger" style="margin-top: 5vh;">
        {{ $errors->first() }}
    </div>
    @endif
    
</body>
</html>