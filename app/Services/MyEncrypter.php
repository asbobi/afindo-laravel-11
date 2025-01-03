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
        $encrypted_base64 = base64_encode($encrypted . '::' . $iv);

        $url_safe_encrypted = strtr($encrypted_base64, '+/', '-_');
        return rtrim($url_safe_encrypted, '=');
    }

    public function decrypt($payload)
    {
        $base64_payload = strtr($payload, '-_', '+/');

        $padding_needed = strlen($base64_payload) % 4;
        if ($padding_needed > 0) {
            $base64_payload .= str_repeat('=', 4 - $padding_needed);
        }

        list($encrypted, $iv) = explode('::', base64_decode($base64_payload), 2);
        return openssl_decrypt($encrypted, 'aes-256-cbc', $this->key, 0, $iv);
    }
}
