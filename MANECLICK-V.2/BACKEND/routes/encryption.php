<?php

function encrypt($data, $key): string
{
    // Define the encryption method
    $method = "AES-256-CBC";

    // Generate a random initialization vector (IV)
    $iv = openssl_random_pseudo_bytes(openssl_cipher_iv_length($method));

    // Encrypt the data
    $encrypted = openssl_encrypt($data, $method, $key, 0, $iv);

    // Concatenate the IV and the encrypted data
    return base64_encode($iv . $encrypted);
}

function decrypt($data, $key): string
{
    // Define the encryption method
    $method = "AES-256-CBC";

    // Decode the encrypted data
    $encrypted = base64_decode($data);

    // Extract the IV and the encrypted data
    $iv = substr($encrypted, 0, openssl_cipher_iv_length($method));
    $encrypted = substr($encrypted, openssl_cipher_iv_length($method));

    // Decrypt the data
    return openssl_decrypt($encrypted, $method, $key, 0, $iv);

}
