<?php

namespace App\Services;

use Encryption\Encryption;
use Illuminate\Support\Facades\Log;

class DecryptRequests {

    protected string $encryptionAlgorithm;

    public function decrypt($encrypted, $key) {
        try {
            $encryption = Encryption::getEncryptionObject($this->encryptionAlgorithm);
            $iv = $this->getIv($this->encryptionAlgorithm);
            $decrypted = $encryption->decrypt($encrypted, $key, $iv, 0);
        } catch (\Throwable $th) {
            Log::error($th->getMessage());
            return NULL;
        }
        return $decrypted;
    }

    public function setAlgorithm($encryptionAlgorithm) {
        $this->encryptionAlgorithm = $encryptionAlgorithm;
    }

    public function getEncryptionAlgorithm() {
        return $this->encryptionAlgorithm;
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