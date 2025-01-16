<?php

namespace App\Services;

class MyEncrypter
{
    protected $key;
    protected $expiry;

    public function __construct($key, $expiry = 86400)
    {
        $this->key = $key;
        $this->expiry = $expiry;
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

    public function encryptWithExpiry($value)
    {
        $expiryTimestamp = strtotime('tomorrow 00:00:00') - 1;
        $data = json_encode([
            'value' => $value,
            'expiry' => $expiryTimestamp
        ]);

        // Use a consistent IV based on the value and expiry
        $iv = substr(hash('sha256', $value . $expiryTimestamp), 0, openssl_cipher_iv_length('aes-256-cbc'));

        $encrypted = openssl_encrypt($data, 'aes-256-cbc', $this->key, 0, $iv);
        $encrypted_base64 = base64_encode($encrypted . '::' . $iv);
        $url_safe_encrypted = strtr($encrypted_base64, '+/', '-_');
        return rtrim($url_safe_encrypted, '=');
    }

    public function decryptWithExpiry($encrypted)
    {
        $encrypted_base64 = strtr($encrypted, '-_', '+/');
        $encrypted_data = base64_decode($encrypted_base64);
        list($encrypted_text, $iv) = explode('::', $encrypted_data, 2);

        $decrypted_data = openssl_decrypt($encrypted_text, 'aes-256-cbc', $this->key, 0, $iv);
        $data = json_decode($decrypted_data, true);

        if (!$data) {
            return null; // Data tidak valid
        }

        // Check if the data has expired
        if (time() > $data['expiry']) {
            return null; // Data sudah expired
        }

        return $data['value'];
    }
}
