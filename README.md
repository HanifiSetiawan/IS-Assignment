# Information Security Assignment
| Name                        | NRP        |
|-----------------------------|------------|
|Mikhael Aryasatya            | 5025211062 |
|Hanifi Abrar Setiawan        | 5025211066 |
|I Putu Arya Prawira W.       | 5025211065 |
|Vija Wildan Gita Prabawa     | 5025211261 |

## Assigment 1
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

## Assignment 2
- Continuing from the previous assignment, add the following features to your web app:
    - A User can request to see private data of other users (including the images and documents)
    - The requested user generates a symmetric key to share the data to the requesting user
    - The symmetric key will be distributed to the requesting user by other means (e.g., email, WA, etc) but it needs to be encrypted by using asymetric encrytion
    - The Symmetric keys must be stored securely in the system

## Login Page
![Login Page](https://media.discordapp.net/attachments/893030036700012585/1165212066412515389/Screenshot_668.png?ex=6546074d&is=6533924d&hm=5a64eb72965104145ef75cfca5c0eda0e9044da0274404a2fa47689f3af6b702&=&width=1248&height=702)
### Controller
#### Login
```php
<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rules\Password;

class LoginController extends Controller
{
    public function index() {
        return view('login');
    }

    public function login(Request $request) {
        $validator = $request->validate([
            'email' => 'required|email',
            'password' => 'required'
        ]);

        if(Auth::attempt($validator)) {
            $request->session()->regenerate();
            return redirect()->intended('/form');
        }

        return back()->withErrors([
            'email' => 'The provided credentials do not match our records.',
        ])->onlyInput('email');

    }
    
}
```
#### Logout
```php
<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LogoutController extends Controller
{
    public function logout() {
        Auth::logout();

        return redirect('login');
    }
}
```

## Register Page
![Register](https://media.discordapp.net/attachments/893030036700012585/1165212067343646791/Screenshot_669.png?ex=6546074d&is=6533924d&hm=b3c6cde896b0549e8c76cf45eecee8742badfb9f500d0255650bb798e3b1f9fc&=&width=1248&height=702)

### Controller
```php
<?php

namespace App\Http\Controllers;

use App\Models\Key;
use App\Models\User;
use App\Services\EncryptRequests;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rules\Password;
use Spatie\Crypto\Rsa\KeyPair;

class RegisterController extends Controller
{
    protected EncryptRequests $encryptRequests;

    public function __construct(EncryptRequests $encryptRequests) {
        $this->encryptRequests = $encryptRequests;
        $encAlgo = config('app.picked_cipher');
        $this->encryptRequests->setAlgorithm($encAlgo);
    }
    public function index() {
        return view('register');
    }

    public function register(Request $request) {
        $validator = Validator::make($request->all(), [
            'name' => ['required', 'ascii'],
            'email' => ['required', 'email', 'unique:users,name'],
            'password' => ['required', Password::min(8)
                                            ->letters()
                                            ->mixedCase()
                                            ->numbers()]
        ]);

        if($validator->stopOnFirstFailure()->fails()) {
            return back()->withErrors($validator);
        }

        $encryptor = function ($data, $key) {
            return $this->encryptRequests->encrypt_with_key($data, $key);
        };

        $validated = $validator->validated();

        $app_key = config('app.key');

        
        $user_key = random_bytes(32);

        $user = new User;
        $user->name = $validated['name'];
        $user->email = $validated['email'];
        $user->password = bcrypt($validated['password']);
        $user->save();

        $key_user = new Key;
        $key_user->key = $encryptor($user_key, $app_key);
        $key_user->user_id = $user->id;
        $key_user->type = 'sym';
        if(empty($key_user->key))
            return back()->withErrors(['encfail' => 'key encryption process (symmetrical) has failed']);   

        $key_user->save();

        $asym = $this->createKeys();

        $key_priv = new Key;
        $key_priv->key = $encryptor($asym['private'], $app_key);
        $key_priv->user_id = $user->id;
        $key_priv->type = 'priv';
        if(empty($key_priv->key))
            return back()->withErrors(['encfail' => 'key encryption process (private) has failed']);
        $key_priv->save();


        $key_pub = new Key;
        $key_pub->key = $encryptor($asym['public'], $app_key);
        $key_pub->user_id = $user->id;
        $key_pub->type = 'pub';
        if(empty($key_pub->key))
        return back()->withErrors(['encfail' => 'key encryption process (public) has failed']);
        $key_pub->save();


        return view('login');

    }

    private function createKeys() {
        $config = [
            'private_key_bits' => 2048,
            'private_key_type' => OPENSSL_KEYTYPE_RSA,
        ];

        $pkey = openssl_pkey_new($config);

        if ($pkey == false) {
            $config['config'] = '/opt/homebrew/etc/openssl@3/openssl.cnf';
        }


        $pkey = openssl_pkey_new($config);
        openssl_pkey_export($pkey, $privateKey, NULL, $config);

        $publicKey = openssl_pkey_get_details($pkey);
        $publicKey = $publicKey["key"];

        return ['private' => $privateKey, 'public' => $publicKey];
    }
}
```

## Home Page 1
![Home](https://cdn.discordapp.com/attachments/1160875961550647348/1177808498822750229/image.png?ex=6573daa4&is=656165a4&hm=b40cc0b43b0e5e8c67f7ccc4015fd227e09a139492f50073fc9864d9f0bda124&)

## Home Page 2
![Home-2](https://cdn.discordapp.com/attachments/1160875961550647348/1177808668838862848/image.png?ex=6573dacd&is=656165cd&hm=475bd6d6c0ee08715d0f4c4ce238c2ff7b888b27ab807107d417560b1beb6e62&)

### Controller
```php
<?php

namespace App\Http\Controllers;

use App\Mail\SendKey;
use App\Models\DataRequest;
use App\Models\User;
use Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Mail;

use App\Services\DecryptRequests;
use App\Services\EncryptRequests;

class HomeController extends Controller
{
    protected $decryptRequests;
    protected $encryptRequests;

    public function __construct(DecryptRequests $decryptRequests, EncryptRequests $encryptRequests) {
        $encAlgo = config('app.picked_cipher');
        $this->decryptRequests = $decryptRequests;
        $this->decryptRequests->setAlgorithm($encAlgo);
        $this->encryptRequests = $encryptRequests;
        $this->encryptRequests->setAlgorithm($encAlgo);
    }
    public function index() {
        $user = Auth::user();

        $sentDataRequests = DataRequest::where('from', '=', $user->email)->get();
        $incomingDataRequests = DataRequest::where('to','=', $user->email)->get();

        return view('home', ['user' => $user->name, 'sent' => $sentDataRequests, 'incoming' => $incomingDataRequests]);
    }

    public function incoming(Request $request) {
        
        $validator = Validator::make($request->all(), [
            'state' => ['required', Rule::in(['accepted', 'rejected'])],
            'from' => ['required', 'email'],
            'to' => ['required', 'email']
        ]);

        if($validator->fails()) {
            return back()->withErrors($validator);
        }

        $validated = $validator->validated();

        
        $data = DataRequest::where('from','=', $validated['from'])->where('to','=', $validated['to'])->first();

        $data->state = $validated['state'];
        $data->save();

        return back()->with('success','Request responded successfully');
    }

    public function send(Request $request) {
        $validator = Validator::make($request->all(), [
            'state' => ['required', Rule::in(['email'])],
            'from' => ['required', 'email'],
            'to' => ['required', 'email']
        ]);

        if($validator->fails()) {
            return back()->withErrors($validator);
        }

        $user = Auth::user();

        $validated = $validator->validated();

        $respondeeExists = User::where('email','=', $validated['to'])->exists();

        if(!$respondeeExists) {
            return back()->with('error',"User doesn't exist");
        }

        $respondee = User::where('email','=', $validated['from'])->first();
        $public = $respondee->getUserKey('pub');

        
        $decryptor = function ($data, $key) {
            return $this->decryptRequests->decrypt($data, $key);
        };

        $app_key = config('app.key');

        $dec_public = $decryptor($public, $app_key);
        if(empty($dec_public)) return redirect()->back()->with('error','Public key decryption has failed');

        $dec_public = openssl_pkey_get_public($dec_public);
        $user_symkey = $user->getUserKey('sym');

        openssl_public_encrypt($user_symkey, $encryptedData, $dec_public);
        

        Mail::to($validated['from'])->send(new SendKey($validated['to'], $encryptedData));


        return back()->with('success','Email sent successfully');
    }
}
```

## Form Page
![Form](https://media.discordapp.net/attachments/893030036700012585/1165212075300241508/Screenshot_671.png?ex=6546074f&is=6533924f&hm=7c70b1656dd44bce45f434cde7042849ba7f48ea3810cedd6a79a8b372ec8d85&=&width=1248&height=702)

### Orang Controller
```php
<?php

namespace App\Http\Controllers;

use App\Models\Key;
use App\Services\DecryptRequests;
use App\Services\EncryptRequests;
use Illuminate\Http\Request;
use App\Models\Orang;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class OrangController extends Controller
{
    protected EncryptRequests $encryptRequests;
    protected DecryptRequests $decryptRequests;


    public function __construct(EncryptRequests $encryptRequests, DecryptRequests $decryptRequests) {
        $this->encryptRequests = $encryptRequests;
        $this->decryptRequests = $decryptRequests;
        $encAlgo = config('app.picked_cipher');
        $this->encryptRequests->setAlgorithm($encAlgo);
        $this->decryptRequests->setAlgorithm($encAlgo);
    }

    public function index()
    {
        return view('form');
    }

    public function simpanData(Request $request)
    {
        
        $time_start = microtime(true);
        // Validasi data yang dikirimkan oleh pengguna
        $validator = Validator::make($request->all(),
        [
            'nama' => 'required|string',
            'nomor_telepon' => 'required|string',
            'foto_ktp' => 'required|image',
            'dokumen' => 'required|mimes:pdf,doc,docx,xls,xlsx',
            'video' => 'required|file|max:25000|mimetypes:video/*',
        ]
        );


        if($validator->fails()) {
            return redirect()->back()->withErrors($validator);
        }

        $encryptor = function ($data, $key) {
            return $this->encryptRequests->encrypt_with_key($data, $key);
        };
        $decryptor = function ($data, $key) {
            return $this->decryptRequests->decrypt($data, $key);
        };

        $user = Auth::user();
        $app_key = config('app.key');
        $key = $decryptor($user->getUserKey('sym'), $app_key);
        if(empty($key)) return redirect()->back()->with('error','Symmetrical Key Decryption has failed');


        $nama = $encryptor($request->input('nama'), $key);
        if(empty($nama)) return redirect()->back()->with('error','Name encryption has failed');

        $no_telp = $encryptor($request->input('nomor_telepon'), $key);
        if(empty($no_telp)) return redirect()->back()->with('error','Phone number encryption has failed');


        $foto = $request->file('foto_ktp');
        $dokumen = $request->file('dokumen');
        $video = $request->file('video');



        $dokumen_enc = $encryptor(base64_encode($dokumen->get()), $key);
        if(empty($dokumen_enc)) return redirect()->back()->with('error','Document encryption has failed');

        $foto_enc = $encryptor(base64_encode($foto->get()), $key);
        if(empty($foto_enc)) return redirect()->back()->with('error','Photo encryption has failed');

        $video_enc = $encryptor(base64_encode($video->get()), $key);
        if(empty($video_enc)) return redirect()->back()->with('error','Video encryption has failed');

        
        $filefoto = Str::uuid()->toString();
        $filedokumen = Str::uuid()->toString();
        $filevideo = Str::uuid()->toString();

        Storage::put($filefoto, $foto_enc);
        Storage::put($filedokumen, $dokumen_enc);
        Storage::put($filevideo, $video_enc);

        $orang = new Orang;
        $orang->nama = $nama;
        $orang->nomor_telepon = $no_telp;
        $orang->foto_ktp = $filefoto;
        $orang->ext_foto = $foto->getClientOriginalExtension();
        $orang->dokumen = $filedokumen;
        $orang->ext_doc = $dokumen->getClientOriginalExtension();
        $orang->video = $filevideo;
        $orang->ext_vid = $video->getClientOriginalExtension();
        $orang->user_id = $user->id;
        $orang->save();
        

        $time_finish = microtime(true);

        $difference = $time_finish - $time_start;

        return view('submit', ['time' => $difference]);
    }

}
```

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
![DatabaseP](https://cdn.discordapp.com/attachments/1160875961550647348/1177810994278432818/image.png?ex=6573dcf7)
![DatabaseV](https://cdn.discordapp.com/attachments/1160875961550647348/1177811247492775937/ezgif-2-695ee5ed88.gif?ex=6573dd34&is=65616834&hm=8a2c5c1295445508cc2c1060de78fcdd28cd42353ca52884b4c6)

### Controller

```php
<?php

namespace App\Http\Controllers;
use App\Models\Orang;
use App\Services\DecryptRequests;
use App\Services\EncryptRequests;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Response;

class datacontroller extends Controller
{
    protected $decryptRequests;
    protected $encryptRequests;

    public function __construct(DecryptRequests $decryptRequests, EncryptRequests $encryptRequests) {
        $encAlgo = config('app.picked_cipher');
        $this->decryptRequests = $decryptRequests;
        $this->decryptRequests->setAlgorithm($encAlgo);
        $this->encryptRequests = $encryptRequests;
        $this->encryptRequests->setAlgorithm($encAlgo);
    }
    public function index(){

        $time_start = microtime(true);

        $decryptor = function ($data, $key) {
            return $this->decryptRequests->decrypt($data, $key);
        };

        $user = Auth::user();

        if($user) {
            $app_key = config('app.key');

            $key = $decryptor($user->getUserKey('sym'), $app_key);
            if(empty($key)) return redirect()->back()->with('error','Symmetrical Key Decryption has failed');


            $exist = $user->orangs()->exists();
            if(!$exist) return view('show'); 

            $orangs = $user->orangs()->get();
            foreach ($orangs as $orang) {

                $pic = Storage::get($orang->foto_ktp);


                $orang->nama = $decryptor($orang->nama, $key);
                $orang->nomor_telepon = $decryptor($orang->nomor_telepon, $key);

                $foto_ktp_dec = $decryptor($pic, $key);
                $orang->foto_ktp = $foto_ktp_dec;    
        }

            $time_finish = microtime(true);

            $difference = $time_finish - $time_start;
            return view('show', ['orangs' => $orangs, 'time' => $difference, 'route' => 'show.download']);
        }
        return redirect()->back()->with('error','User invalid');
    }

    public function download($orang_id, $ext, $file) {

        $decryptor = function ($data, $key) {
            return $this->decryptRequests->decrypt($data, $key);
        };

        $user = Auth::user();
        $app_key = config('app.key');
        $key = $decryptor($user->getUserKey('sym'), $app_key);
        if(empty($key)) return redirect()->back()->with('error','Symmetrical Key Decryption has failed');


        $doc = Storage::get($file);
        $dok_dec = $decryptor($doc, $key);
        if(empty($dok_dec)) return redirect()->back()->with('error','File Decryption has failed');


        $filepath = 'file' . '.' . $ext;
        Storage::put($filepath, base64_decode($dok_dec));
        $response = response()->download(Storage::path($filepath))->deleteFileAfterSend(true);


        return $response;
    }
}
```

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
## Request Access
![RequestPage](https://cdn.discordapp.com/attachments/1160875961550647348/1177811258649612410/image.png?ex=6573dd36&is=65616836&hm=7db8d16b6cca61ff59c920b19098d5302d5861ca71df80d7ae9aceff4cd19365&)

## Shared Access
![SharedPage](https://cdn.discordapp.com/attachments/1160875961550647348/1177811518277025842/image.png?ex=6573dd74&is=65616874&hm=d843d1fbaac0d9918d8bd28c7fa1033786f3152bf65ea66d14cf17a68e498172&)

### Controller
```php
<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Auth;
use App\Services\DecryptRequests;
use App\Services\EncryptRequests;
use App\Models\Key;
use App\Models\User;
use Illuminate\Support\Facades\Storage;

class SharedAccessController extends Controller
{

    protected $decryptRequests;
    protected $encryptRequests;

    public function __construct(DecryptRequests $decryptRequests, EncryptRequests $encryptRequests) {
        $encAlgo = config('app.picked_cipher');
        $this->decryptRequests = $decryptRequests;
        $this->decryptRequests->setAlgorithm($encAlgo);
        $this->encryptRequests = $encryptRequests;
        $this->encryptRequests->setAlgorithm($encAlgo);
    }
    public function index() {
        return view("share-access");
    }

    public function show(Request $request) {

        $time_start = microtime(true);

        $validator = Validator::make($request->all(), [
            'key' => ['required','string']
        ]);
        
        if($validator->stopOnFirstFailure()->fails()) {
            return back()->withErrors($validator);
        }
        
        $validated = $validator->validated();
        $user = Auth::user();
        $key = base64_decode($validated['key']);


        $decryptor = function ($data, $key) {
            return $this->decryptRequests->decrypt($data, $key);
        };

        $app_key = config('app.key');
        $usr_priv = $decryptor($user->getUserKey('priv'), $app_key);
        $usr_priv = openssl_pkey_get_private($usr_priv);

        openssl_private_decrypt($key, $sym_key, $usr_priv);

        if(empty($sym_key)) {
            return back()->withErrors(['decfail' => 'Decryption has failed (private key)']);
        }

        $s_userid = Key::where('key', $sym_key)->first()->user_id;

        $s_user = User::find($s_userid);

        if($s_user) {

            $key = $decryptor($sym_key, $app_key);
            if(empty($key)) return redirect()->back()->with('error','Symmetrical Key Decryption has failed');


            $exist = $s_user->orangs()->exists();
            if(!$exist) return view('show'); 

            $orangs = $s_user->orangs()->get();
            foreach ($orangs as $orang) {

                $pic = Storage::get($orang->foto_ktp);


                $orang->nama = $decryptor($orang->nama, $key);
                $orang->nomor_telepon = $decryptor($orang->nomor_telepon, $key);

                $foto_ktp_dec = $decryptor($pic, $key);
                $orang->foto_ktp = $foto_ktp_dec;

            }
            $time_finish = microtime(true);

            $difference = $time_finish - $time_start;
            session(['check-access-route' => true, 's_user' => $s_user]);

            return view('show', ['orangs' => $orangs, 'time' => $difference, 'route' => "shared.download"]);
        }
        return redirect()->back()->with('error','User invalid');
        
    }

    public function download($orang_id, $ext, $file) {

        $decryptor = function ($data, $key) {
            return $this->decryptRequests->decrypt($data, $key);
        };

        $user = session('s_user');
        $app_key = config('app.key');
        $key = $decryptor($user->getUserKey('sym'), $app_key);
        if(empty($key)) return redirect()->back()->with('error','Symmetrical Key Decryption has failed');


        $doc = Storage::get($file);
        $dok_dec = $decryptor($doc, $key);
        if(empty($dok_dec)) return redirect()->back()->with('error','File Decryption has failed');


        $filepath = 'file' . '.' . $ext;
        Storage::put($filepath, base64_decode($dok_dec));
        $response = response()->download(Storage::path($filepath))->deleteFileAfterSend(true);


        return $response;
    }
}
```
