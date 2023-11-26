<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Support\Facades\Storage;


class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];

    public function orangs()
    {
        return $this->hasMany(Orang::class, 'user_id');
    }

    public function keys()
    {
        return $this->hasMany(Key::class, 'user_id');
    }

    public function getUserKeys(string $type)
    {
        $key = $this->keys()->where('type', $type)->get();


        if ($key) {
            return $key;
        }   

        return null;
    }

    public function getDecryptedOrangs($decryptor) {
        $app_key = config('app.key');
        $keys = $this->getUserKeys('sym');

        $orangs = array();

        foreach ($keys as $key_model) {
            $orang = Orang::where('key_id', $key_model->id)->first();
            if(!$orang->exists()) return null;

            $key = $decryptor($key_model->key, $app_key);

            $pic = Storage::get($orang->foto_ktp);
            $orang->nama = $decryptor($orang->nama, $key);
            $orang->nomor_telepon = $decryptor($orang->nomor_telepon, $key);

            $foto_ktp_dec = $decryptor($pic, $key);
            $orang->foto_ktp = $foto_ktp_dec;

            array_push($orangs, $orang);
        }

        return $orangs;
    }

    public function getAsymmetricKey($decryptor, $type) {
        $app_key = config('app.key');
        $key = $this->keys()->where('type', $type)->first();
        if(!$key->exists()) return null;
        

        return $decryptor($key->key, $app_key);
    }
}
