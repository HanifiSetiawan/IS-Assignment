<?php

namespace App\Services;

use Encryption\Encryption;

class DecryptRequests {

    protected string $encryptionAlgorithm;

    public function decrypt($encrypted, $key) {
        try {
            $iv = substr(config('app.key'), 0, 16);
            $encryption = Encryption::getEncryptionObject($this->encryptionAlgorithm);
            $decrypted = $encryption->decrypt($encrypted, $key, $iv, 0);
        } catch (\Throwable $th) {
            print($th);
            return NULL;
        }
        return $decrypted;
    }

    public function setAlgorithm($encryptionAlgorithm) {
        $this->encryptionAlgorithm = $encryptionAlgorithm;
    }
}