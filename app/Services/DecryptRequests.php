<?php

namespace App\Services;

use Encryption\Encryption;

class DecryptRequests {
    public function decrypt(string $type, $encrypted, $key, $iv) {
        try {
            $key = base64_decode($key);
            $iv = base64_decode($iv);
            $encryption = Encryption::getEncryptionObject($type);
            $decrypted = $encryption->decrypt($encrypted, $key, $iv, 0);
        } catch (\Throwable $th) {
            return back()->withErrors([
                'decryption' => 'Decryption has failed',
            ]);
        }
        return $decrypted;
    }
}