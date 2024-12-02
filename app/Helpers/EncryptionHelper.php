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
