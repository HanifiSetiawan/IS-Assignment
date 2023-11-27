<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.1/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-4bw+/aepP/YC94hEpVNVgiZdgIC5+VKNBQNGCHeKRQN+PtmoHDEXuppvnDJzQIu9" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/css/bootstrap.min.css">
    <link rel="stylesheet" href="{{ asset('css/login.css') }}">
</head>
<body>
    <form action="{{ route('login') }}" method="POST" enctype="multipart/form-data">
        @csrf
        <h1>Login</h1>
        <div class="form-group">
            <label for="email">Email</label>
            <input type="email" name="email" class="form-control">
        </div>
        <div class="form-group">
            <label for="password">Password</label>
            <input type="password" name="password" class="form-control">
        </div>
        <button type="submit" id="submitbutton" class="btn btn-primary">Login</button>
        <a class="btn btn-success" id="submitbutton" href="{{route('register')}}">Register</a>
        @if ($errors->any())
            <div class="alert alert-danger" style="margin-top: 5vh;">
                {{ $errors->first() }}
            </div>
        @endif
    </form>
</body>
</html>
