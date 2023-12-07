<head>
    <link rel="stylesheet" href="{{ asset('css/shared-access.css') }}">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.1/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-4bw+/aepP/YC94hEpVNVgiZdgIC5+VKNBQNGCHeKRQN+PtmoHDEXuppvnDJzQIu9" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/css/bootstrap.min.css">
    <title>Shared Data</title>
</head>



@include('navbar')
<div>
    <div class="boxing">
        <h3>Insert File</h3>
        <hr>
        <form action="{{route('verify.submit')}}" method="post" enctype="multipart/form-data">
            @csrf
            <div class="form-group">
                <label for="file">File:</label>
                <input type="file" name="file" class="form-control">

                <button type="submit" id="submitbutton" class="btn btn-primary" style="margin-top: 1vh;">Request</button>
                @if(session()->has('success'))
                <div class="alert alert-success" style="margin-top: 5vh;">
                    {{ session('success') }}
                </div>
                @endif
                @if ($errors->any())
                <div class="alert alert-danger" style="margin-top: 5vh;">
                    {{ $errors->first() }}
                </div>
                @endif
            </div>
    </div>
</div>
