<?php

if (!function_exists('my_encrypt')) {
    function my_encrypt($value)
    {
        return app('my-encrypter')->encrypt($value);
    }
}

if (!function_exists('my_decrypt')) {
    function my_decrypt($value)
    {
        return app('my-encrypter')->decrypt($value);
    }
}

if (!function_exists('my_encrypt_aday')) {
    function my_encrypt_aday($value)
    {
        return app('my-encrypter')->encryptWithExpiry($value);
    }
}

if (!function_exists('my_decrypt_aday')) {
    function my_decrypt_aday($value)
    {
        return app('my-encrypter')->decryptWithExpiry($value);
    }
}
