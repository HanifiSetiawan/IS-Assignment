<?php

namespace App\Services;

use Encryption\Encryption;

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
            $iv = substr(config('app.key'), 0, 16);
            $tag = 0;

            $encrypted = $encryption->encrypt($data, $key, $iv, $tag);
            
        } catch (\Throwable $th) {
            print($th);
            return NULL;
        }
        
        return $encrypted;
    }

    public function setAlgorithm($encryptionAlgorithm) {
        $this->encryptionAlgorithm = $encryptionAlgorithm;
    }
}