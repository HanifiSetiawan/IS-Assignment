<?php

namespace App\Services;

use Encryption\Encryption;
use Illuminate\Support\Facades\Log;


class EncryptRequests {

    protected string $encryptionAlgorithm;

    public function encrypt($data) {

        try {
            $encryption = Encryption::getEncryptionObject($this->encryptionAlgorithm);
            $iv = $encryption->generateIv();
            $key = random_bytes(32);
            $tag = 0;

            $encrypted = $encryption->encrypt($data, $key, $iv, $tag);
            
        } catch (\Throwable $th) {
            return back()->withErrors([
                'encryption' => 'Encryption has failed',
            ]);
        }
        
        return ['enc' => $encrypted, 'key' => base64_encode($key), 'iv' => base64_encode($iv)];

        
    }

    public function encrypt_with_key($data, $key) {
        try {
            $encryption = Encryption::getEncryptionObject($this->encryptionAlgorithm);
            $iv = $this->getIv($this->encryptionAlgorithm);
            $tag = 0;

            $encrypted = $encryption->encrypt($data, $key, $iv, $tag);
            
        } catch (\Throwable $th) {
            Log::error($th->getMessage());
            return NULL;
        }
        
        return $encrypted;
    }

    public function setAlgorithm($encryptionAlgorithm) {
        $this->encryptionAlgorithm = $encryptionAlgorithm;
    }

    function getIv($algorithm) {
        switch ($algorithm) {
            case 'AES-256-CBC':
                $iv = substr(config('app.key'), 0, 16);
                break;
            case 'DES-CBC':
                $iv = substr(config('app.key'), 0, 8);
                break;
            default:
                $iv = '0';
                break;
        }
        return $iv;
    }
}