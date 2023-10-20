<?php

namespace App\Services;

use Encryption\Encryption;

class DecryptRequests {

    protected string $encryptionAlgorithm;

    public function decrypt($encrypted, $key, $iv) {
        try {
            $key = base64_decode($key);
            $iv = base64_decode($iv);
            $encryption = Encryption::getEncryptionObject($this->encryptionAlgorithm);
            $decrypted = $encryption->decrypt($encrypted, $key, $iv, 0);
        } catch (\Throwable $th) {
            return back()->withErrors([
                'decryption' => 'Decryption has failed',
            ]);
        }
        return $decrypted;
    }

    public function setAlgorithm($encryptionAlgorithm) {
        $this->encryptionAlgorithm = $encryptionAlgorithm;
    }
}