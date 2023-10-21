<?php

namespace App\Services;

use Encryption\Encryption;

class DecryptRequests {

    protected string $encryptionAlgorithm;

    public function decrypt($encrypted, $key) {
        try {
            $encryption = Encryption::getEncryptionObject($this->encryptionAlgorithm);
            $iv = $this->getIv($this->encryptionAlgorithm);
            $decrypted = $encryption->decrypt($encrypted, $key, $iv, 0);
        } catch (\Throwable $th) {
            return NULL;
        }
        return $decrypted;
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