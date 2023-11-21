<?php

use Encryption\Cipher\RC4\Rc4;
use phpseclib3\Crypt\AES;
use phpseclib3\Crypt\TripleDES;
use phpseclib3\Crypt\Random;
use phpseclib3\Crypt\EC;


class Encryptor {

    public static function Encrypt($cipherClass, $key, $data) {
        $classname = $cipherClass.'Class';

        if($cipherClass == 'AES' or $cipherClass == 'TripleDES') {
            $cipher = new $classname('cbc');
            if($cipherClass == 'AES') {
                $app_key = substr(config('app.key'), 0, 16);
            }
            else if($cipherClass == 'TripleDES') {
                $app_key = substr(config('app.key'), 0, 8);
            }
            else {
                $app_key = 0;
            }

            $cipher->setIV($app_key);
            $cipher->setKey($key);

            return $cipher->encrypt($data);
        }

    }
    public static function AES($key, $data) {
        $app_key = substr(config('app.key'), 0, 16);
        $cipher = new AES('cbc');
        $cipher->setIV($app_key);
        $cipher->setKey($key);

        return $cipher->encrypt($data);
    }

    public static function DES($key, $data) {
        $app_key = substr(config('app.key'), 0, 8);
        $cipher = new TripleDES('cbc');
        $cipher->setIV($app_key);
        $cipher->setKey($key);

        return $cipher->encrypt($data);
    }

    public static function Rc4($key, $data) {
        $cipher = new Rc4;
        return $cipher->encrypt($data, $key);
    }
}