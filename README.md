# Information Security Assignment
| Name                        | NRP        |
|-----------------------------|------------|
|Mikhael Aryasatya            | 5025211062 |
|Hanifi Abrar Setiawan        | 5025211066 |
|I Putu Arya Prawira W.       | 5025211065 |
|Vija Wildan Gita Prabawa     | 5025211261 |

## Assigment
- Create a web application that can:
    - Store user’s private data in a database
    - Refer to GDPR (EU)/UU PDP for what are considered to be private data
    - Store user’s ID card image
    - Store user’s PDF/DOC/XLS files
    - Store user’s video files
- All stored data must be encrypted with all of these algorithms:
    - AES
    - RC4
    - DES
- You need to use one of the non-ECB operation modes for the block cipher (i.e., CBC, CFB, OFB, CTR)
- Users must be able to retrieve their decrypted data
- Users must provide a pair of username and password to access their data
- You may use any cryptography library in any language (e.g., BouncyCastle, Java Crypto, PyCrypto, etc)
- The program must be bug-free to get a full mark
- Analyse the difference (i.e., running time, the resulting cipher text) between each cipher
- Download the data multiple times and measure the running time!
- Write a short report on the analysis result
- Justify all of your decisions during the development
- Plagiarism will get you 0 mark
- All code must be committed to Github and you need to put the link on the following link

## Login Page
![Login Page](https://media.discordapp.net/attachments/893030036700012585/1165212066412515389/Screenshot_668.png?ex=6546074d&is=6533924d&hm=5a64eb72965104145ef75cfca5c0eda0e9044da0274404a2fa47689f3af6b702&=&width=1248&height=702)
### PHP
```php
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
    </form>
</body>
</html>

```

### CSS

```css
h1 {
    text-align: center;
    padding-top: 10px;
    margin: auto;
    margin-bottom: 1vw;
}

form {
    position: relative;
    top: 20vh;
    margin: auto;
    width: 30%;
    height: 55%;
    padding: 1% 3% 5% 3%;
    border: 5px solid black;
    color: rgba(0, 0, 0, 0.966);
    background-color: aliceblue;
}

body {
    background-repeat: no-repeat;
    background-size: cover;
    background-image:url("../images/FTIF-Informatika-01-e1638867228268.jpg")
}
.form-group {
    position: relative;
    display: flex;
    flex-direction: column;
    margin-left: 3%;
    margin-right: 6%;
    margin-top: 3%;
}

.form-group label {
    margin-bottom: 5%;
    scale: 100%;
}

#submitbutton {
    position: relative;
    margin-top: 5vh;
    margin-left: 2vw;
    margin-right: 3vw;
    width: 82.5%;
}
```

## Register Page
![Register](https://media.discordapp.net/attachments/893030036700012585/1165212067343646791/Screenshot_669.png?ex=6546074d&is=6533924d&hm=b3c6cde896b0549e8c76cf45eecee8742badfb9f500d0255650bb798e3b1f9fc&=&width=1248&height=702)

### PHP
```php
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.1/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-4bw+/aepP/YC94hEpVNVgiZdgIC5+VKNBQNGCHeKRQN+PtmoHDEXuppvnDJzQIu9" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/css/bootstrap.min.css">
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
```

### CSS
```css
h1 {
    text-align: center;
    padding-top: 10px;
    margin: auto;
    margin-bottom: 1vw;
}

form {
    position: relative;
    top: 20vh;
    margin: auto;
    width: 30%;
    height: 55%;
    padding: 1% 3% 5% 3%;
    border: 5px solid black;
    color: rgba(0, 0, 0, 0.966);
    background-color: aliceblue;
}

body {
    background-repeat: no-repeat;
    background-size: cover;
    background-image:url("../images/FTIF-Informatika-01-e1638867228268.jpg")
}
.form-group {
    position: relative;
    display: flex;
    flex-direction: column;
    margin-left: 3%;
    margin-right: 6%;
    margin-top: 3%;
}

.form-group label {
    margin-bottom: 5%;
    scale: 100%;
}

#submitbutton {
    position: relative;
    margin-top: 5vh;
    margin-left: 2vw;
    margin-right: 3vw;
    width: 82.5%;
}
```

## Home Page
![Home](https://media.discordapp.net/attachments/893030036700012585/1165212074796929034/Screenshot_670.png?ex=6546074f&is=6533924f&hm=2306ecb358f54ad476622edf4a4e8d0ff6f89daa342d694ec305099fa57777d3&=&width=1248&height=702)

### PHP
```php
@include('navbar')
<div>
    <h1>Welcome to The App</h1>
</div>

```

## Form Page
![Form](https://media.discordapp.net/attachments/893030036700012585/1165212075300241508/Screenshot_671.png?ex=6546074f&is=6533924f&hm=7c70b1656dd44bce45f434cde7042849ba7f48ea3810cedd6a79a8b372ec8d85&=&width=1248&height=702)

### PHP
```php
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
    @include('navbar')
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

```

### CSS
```css
.title {
    color: black;
}

h1 {
    text-align: center;
    padding-top: 10px;
    margin: auto;
    margin-bottom: 1vw;
    color: black;
}

form {
    margin: auto;
    width: 30%;
    height: 55%;
    padding: 10px;
    border: 5px solid black;
    color: rgba(0, 0, 0, 0.966);
    background-color: aliceblue;
}

body {
    background-repeat: no-repeat;
    background-size: cover;
    background-image:url("../images/FTIF-Informatika-01-e1638867228268.jpg")
}
.form-group {
    position: relative;
    display: flex;
    flex-direction: column;
    padding: 1% 7% 5% 7%;
    margin-left: 2%;
    margin-right: 6%;
    margin-top: 2%;
}

.form-group label {
    margin-bottom: 5%;
    scale: 100%;
}

#submitbutton {
    position: relative;
    margin-top: 5vh;
    margin-left: 2vw;
    margin-right: 3vw;
    width: 82.5%;
}

.registerbutton {
    position: relative;
    margin-top: 5vh;
    margin-left: 2vw;
    margin-right: 3vw;
    width: 82.5%;
}
```

## Success Page
![SuccPage](https://media.discordapp.net/attachments/893030036700012585/1165212075925180470/Screenshot_672.png?ex=6546074f&is=6533924f&hm=2e7b69e8a4dea96f5e31cf6fed4e124cad37b18b618b6e9dd040768475d58aeb&=&width=1248&height=702)

### PHP
```php
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
</head>
<body>
    @include('navbar')
    <h1>selamat datamu sudah tersimpan! hehe</h1>

    <p>Time to encrypt : {{$time}} microseconds</p>
</body>
</html>
```

## Database Page
![DatabaseP](https://media.discordapp.net/attachments/893030036700012585/1165212076415926292/Screenshot_673.png?ex=6546074f&is=6533924f&hm=87d086c7aa988c86faf136bf247eb81f56bd207b9eecba9a3059805302f5b42b&=&width=1248&height=702)

### PHP
```php
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
```

### CSS
```css
.foto{
    width: 100px;
    height: auto;
}
```
