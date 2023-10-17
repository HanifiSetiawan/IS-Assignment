<?php

namespace App\Services;

use Encryption\Encryption;

class EncryptRequests {
    public function encrypt($data, string $type) {

        try {
            $encryption = Encryption::getEncryptionObject($type);
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
}