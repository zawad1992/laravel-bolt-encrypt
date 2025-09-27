<?php

if (!function_exists('bolt_decrypt')) {
    /**
     * Decrypt encrypted PHP file content
     */
    function bolt_decrypt(string $file, string $key): void
    {
        $content = file_get_contents($file);
        
        if ($content === false) {
            throw new Exception("Cannot read encrypted file: $file");
        }
        
        // Find the encrypted content after the marker
        $marker = '##!!!##';
        $pos = strpos($content, $marker);
        
        if ($pos === false) {
            throw new Exception("Invalid encrypted file format");
        }
        
        $encryptedData = substr($content, $pos + strlen($marker));
        $decryptedData = bolt_decrypt_data($encryptedData, $key);
        
        // Execute the decrypted PHP code
        eval($decryptedData);
    }
}

if (!function_exists('bolt_decrypt_data')) {
    /**
     * Decrypt data using XOR with the provided key
     */
    function bolt_decrypt_data(string $encryptedData, string $key): string
    {
        $decoded = base64_decode($encryptedData);
        
        if ($decoded === false) {
            throw new Exception("Invalid encrypted data format");
        }
        
        $keyLength = strlen($key);
        $dataLength = strlen($decoded);
        $decrypted = '';
        
        for ($i = 0; $i < $dataLength; $i++) {
            $decrypted .= chr(ord($decoded[$i]) ^ ord($key[$i % $keyLength]));
        }
        
        return $decrypted;
    }
}

if (!function_exists('bolt_encrypt')) {
    /**
     * Encrypt data using XOR with the provided key
     */
    function bolt_encrypt(string $data, string $key): string
    {
        $keyLength = strlen($key);
        $dataLength = strlen($data);
        $encrypted = '';
        
        for ($i = 0; $i < $dataLength; $i++) {
            $encrypted .= chr(ord($data[$i]) ^ ord($key[$i % $keyLength]));
        }
        
        return base64_encode($encrypted);
    }
}