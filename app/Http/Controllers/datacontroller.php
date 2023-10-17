<?php

namespace App\Http\Controllers;
use App\Models\Orang;
use App\Services\DecryptRequests;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Response;

class datacontroller extends Controller
{
    protected $decryptRequests;

    public function __construct(DecryptRequests $decryptRequests) {
        $this->decryptRequests = $decryptRequests;
    }
    public function index(){
        $user = Auth::user();
        $encType = 'aes-256-cbc';

        if($user) {
            $orangs = $user->orangs()->get();
            foreach ($orangs as $orang) {
                $key_nama = $orang->keys()->where('purpose', 'nama')->first();
                $key_notelp = $orang->keys()->where('purpose', 'nomor_telepon')->first();
                $key_pic = $orang->keys()->where('purpose', 'foto_ktp')->first();
                $key_dokumen = $orang->keys()->where('purpose', 'dokumen')->first();
                $key_video = $orang->keys()->where('purpose', 'video')->first();

                $pic = Storage::get($orang->foto_ktp);
                $doc = Storage::get($orang->dokumen);
                $vid = Storage::get($orang->video);


                $orang->nama = $this->decryptRequests
                    ->decrypt($encType,
                            $orang->nama,
                            $key_nama->key,
                            $key_nama->iv);
                
                $orang->nomor_telepon = $this->decryptRequests
                ->decrypt($encType,
                        $orang->nomor_telepon,
                        $key_notelp->key,
                        $key_notelp->iv);

                $foto_ktp_dec = $this->decryptRequests
                    ->decrypt($encType,
                            $pic,
                            $key_pic->key,
                            $key_pic->iv);

                $dok_dec = $this->decryptRequests
                    ->decrypt($encType,
                            $doc,
                            $key_dokumen->key,
                            $key_dokumen->iv);

                $vid_dec = $this->decryptRequests
                    ->decrypt($encType,
                            $vid,
                            $key_video->key,
                            $key_video->iv);

                $orang->foto_ktp = $foto_ktp_dec;

                //not yet
                
            }
            return view('show', ['orangs' => $orangs]);
        }
    }

    public function downloadDocs($orang_id) {
        $encType = 'aes-256-cbc';
        $orang = Orang::find($orang_id);
        $key_dokumen = $orang->keys()->where('purpose', 'dokumen')->first();
        $doc = Storage::get($orang->dokumen);

        $dok_dec = $this->decryptRequests
                    ->decrypt($encType,
                            $doc,
                            $key_dokumen->key,
                            $key_dokumen->iv);
        dd($orang);
    }
}
