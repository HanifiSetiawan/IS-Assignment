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
        $this->decryptRequests = $decryptRequests;
        $this->decryptRequests->setAlgorithm('aes-256-cbc');
        $this->encryptRequests = $encryptRequests;
        $this->encryptRequests->setAlgorithm('aes-256-cbc');
    }
    public function index(){
        $user = Auth::user();

        if($user) {
            $orangs = $user->orangs()->get();
            foreach ($orangs as $orang) {
                $key_nama = $orang->keys()->where('purpose', 'nama')->first();
                $key_notelp = $orang->keys()->where('purpose', 'nomor_telepon')->first();
                $key_pic = $orang->keys()->where('purpose', 'foto_ktp')->first();

                $pic = Storage::get($orang->foto_ktp);


                $orang->nama = $this->decryptRequests
                    ->decrypt(
                            $orang->nama,
                            $key_nama->key,
                            $key_nama->iv);
                
                $orang->nomor_telepon = $this->decryptRequests
                ->decrypt(
                        $orang->nomor_telepon,
                        $key_notelp->key,
                        $key_notelp->iv);

                $foto_ktp_dec = $this->decryptRequests
                    ->decrypt(
                            $pic,
                            $key_pic->key,
                            $key_pic->iv);

                $orang->foto_ktp = $foto_ktp_dec;

                
            }
            return view('show', ['orangs' => $orangs]);
        }
    }

    public function downloadDocs($orang_id) {
        $orang = Orang::find($orang_id);
        $key_dokumen = $orang->keys()->where('purpose', 'dokumen')->first();
        $filedokumen = $orang->dokumen;
        $doc = Storage::get($filedokumen);

        $dok_dec = $this->decryptRequests
                    ->decrypt(
                            $doc,
                            $key_dokumen->key,
                            $key_dokumen->iv);
        Storage::delete($filedokumen);

        $filepath = $filedokumen . '.' . $orang->ext_doc;
        Storage::put($filepath, base64_decode($dok_dec));

        $response = response()->download(Storage::path($filepath))->deleteFileAfterSend(true);

        $doc = Storage::get($filepath);

        $dokumen_enc = $this->encryptRequests->encrypt(base64_encode($doc));
        Storage::put($filedokumen, $dokumen_enc['enc']);

        $key_dokumen->key = $dokumen_enc['key'];
        $key_dokumen->iv = $dokumen_enc['iv'];
        $key_dokumen->save();


        return $response;
    }

    public function downloadVids($orang_id) {
        $orang = Orang::find($orang_id);
        $key_video = $orang->keys()->where('purpose', 'video')->first();
        $filevideo = $orang->video;
        $vid = Storage::get($filevideo);

        $vid_dec = $this->decryptRequests
                    ->decrypt(
                            $vid,
                            $key_video->key,
                            $key_video->iv);
        Storage::delete($filevideo);

        $filepath = $filevideo . '.' . $orang->ext_vid;
        Storage::put($filepath, base64_decode($vid_dec));

        $response = response()->download(Storage::path($filepath))->deleteFileAfterSend(true);
        
        $vid = Storage::get($filepath);

        $vid_enc = $this->encryptRequests->encrypt(base64_encode($vid));
        Storage::put($filevideo, $vid_enc['enc']);

        $key_video->key = $vid_enc['key'];
        $key_video->iv = $vid_enc['iv'];
        $key_video->save();

        return $response;
    }
}
