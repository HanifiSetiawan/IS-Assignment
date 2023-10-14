<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register</title>
    <link rel="stylesheet" href="{{ asset('css/login.css') }}">
</head>
<body>
    <form action="{{ route('register') }}" method="POST" enctype="multipart/form-data">
        @csrf
        <h1>Register</h1>
        <div class="form-group">
            <label for="name">Full name</label>
            <input type="text" name="name" class="form-control">
            @error('name')
            <div class="error">{{ $message }}</div>
            @enderror
        </div>
        <div class="form-group">
            <label for="email">Email</label>
            <input type="email" name="email" class="form-control">
            @error('email')
            <div class="error">{{ $message }}</div>
            @enderror
        </div>
        <div class="form-group">
            <label for="password">Password</label>
            <input type="password" name="password" class="form-control">
            @error('password')
            <div class="error">{{ $message }}</div>
            @enderror
        </div>
        <button type="submit" id="submitbutton" class="btn btn-primary">Register</button>
    </form>

</body>
</html>
