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
        $key = random_bytes(32);

        $nama = $encryptor($request->input('nama'), $key);
        if(empty($nama)) return redirect()->back()->with('error','Name encryption has failed');

        $no_telp = $encryptor($request->input('nomor_telepon'), $key);
        if(empty($no_telp)) return redirect()->back()->with('error','Phone number encryption has failed');


        $foto = $request->file('foto_ktp');
        $dokumen = $request->file('dokumen');
        $video = $request->file('video');

        $dokumen_hash = bcrypt($dokumen->get());
        //then encrypt the hash

        //regex pattern to get pdf object -> 2 0 obj, for example
        $pattern = '/(\d+) (\d+) obj/';
        
        $f = $dokumen->getContent();
        $maxObj = -1;
        $offsetEOF = -1;

        // will get the maximum object number
        // making sure that the pdf file doesn't use the object number, iterating by 1
        if (preg_match_all($pattern, $f, $matches, PREG_SET_ORDER)) {
            foreach ($matches as $match) {
                if($match[1] > $maxObj){
                    $maxObj = $match[1];
                }
            }
            //find offset of last %%EOF
            if(preg_match("/%%EOF$/", $f, $matches, PREG_OFFSET_CAPTURE)) {
                $offsetEOF = $matches[0];
            }
            else return redirect()->back()->with('error','something wrong with pdf (1)');

        }
        else return redirect()->back()->with('error','something wrong with pdf (2)');
        //iterate +1 to use as a new object later
        $maxObj += 1;
        $offsetEOF = $offsetEOF[1];
        
        //hopefully stitching the string containing the dig sig
        $before = substr($f, 0, $offsetEOF);
        $after = substr($f, $offsetEOF);

        //continue trying to understand how to embed dig sig to pdf
        $digSigString = $maxObj . " 0 obj
        <</F 132/Type/Annot/Subtype/Widget/Rect[0 0 0 0]/FT/Sig
        /DR<<>>/T(signature)/V 1 0 R/P 4 0 R/AP<</N 2 0 R>>>>
        endobj\n";
        $f = $before . $digSigString . $after;
        dd($f);




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

        $sym_key = new Key;
        $sym_key->key = $encryptor($key, $app_key);
        $sym_key->user_id = $user->id;
        $sym_key->type = 'sym';
        $sym_key->save();

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
        $orang->key_id = $sym_key->id;
        $orang->save();

        
        

        $time_finish = microtime(true);

        $difference = $time_finish - $time_start;

        return view('submit', ['time' => $difference]);
    }

}

