<head>
    <title>Request Access</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.1/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-4bw+/aepP/YC94hEpVNVgiZdgIC5+VKNBQNGCHeKRQN+PtmoHDEXuppvnDJzQIu9" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/css/bootstrap.min.css">
    <link rel="stylesheet" href="{{ asset('css/form.css') }}">
</head>
<body>
    @include('navbar')
    <div>
        <div class="title">
            <h1 class="text-black ">Request Access</h1>
        </div>
        <form action="{{route('access.submit')}}" method="post" enctype="multipart/form-data">
            @csrf
            <div class="form-group">
                <label for="email">Email:</label>
                <input type="email" name="email" class="form-control">

                <button type="submit" id="submitbutton" class="btn btn-primary">Request</button>
            </div>
        </form>
    </div>
</body>

