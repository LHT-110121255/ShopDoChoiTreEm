<?php
// helpers/EncryptionHelper.php

class EncryptionHelper
{
    private static $method = 'AES-256-CBC'; // Phương thức mã hóa
    private static $key;

    public static function setKey($key)
    {
        self::$key = $key;
    }

    public static function encrypt($data)
    {
        $iv = openssl_random_pseudo_bytes(openssl_cipher_iv_length(self::$method));
        $encrypted = openssl_encrypt($data, self::$method, self::$key, 0, $iv);
        return base64_encode($encrypted . '::' . $iv); // Ghép mã hóa + IV
    }

    public static function decrypt($data)
    {
        $data = base64_decode($data);
        list($encryptedData, $iv) = explode('::', $data, 2);
        return openssl_decrypt($encryptedData, self::$method, self::$key, 0, $iv);
    }
}
