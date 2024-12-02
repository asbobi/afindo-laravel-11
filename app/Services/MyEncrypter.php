<?php

namespace App\Services;

class MyEncrypter
{
    protected $key;

    public function __construct($key)
    {
        $this->key = $key;
    }

    public function encrypt($value)
    {
        $iv = openssl_random_pseudo_bytes(openssl_cipher_iv_length('aes-256-cbc'));
        $encrypted = openssl_encrypt($value, 'aes-256-cbc', $this->key, 0, $iv);
        return base64_encode($encrypted . '::' . $iv);
    }

    public function decrypt($payload)
    {
        list($encrypted, $iv) = explode('::', base64_decode($payload), 2);
        return openssl_decrypt($encrypted, 'aes-256-cbc', $this->key, 0, $iv);
    }
}
