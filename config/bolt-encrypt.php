<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Default Encryption Key
    |--------------------------------------------------------------------------
    |
    | This key is used to encrypt your PHP files. You should set this to a
    | secure, random string. Keep this key secret and don't share it.
    |
    */
    'encryption_key' => env('BOLT_ENCRYPT_KEY', 'kyc7fh'),

    /*
    |--------------------------------------------------------------------------
    | Default Source Path
    |--------------------------------------------------------------------------
    |
    | The default source directory to encrypt files from. This can be
    | overridden when using the encryption service or command.
    |
    */
    'source_path' => env('BOLT_ENCRYPT_SOURCE', 'src/app'),

    /*
    |--------------------------------------------------------------------------
    | Default Output Path
    |--------------------------------------------------------------------------
    |
    | The default output directory where encrypted files will be stored.
    | This can be overridden when using the encryption service or command.
    |
    */
    'output_path' => env('BOLT_ENCRYPT_OUTPUT', 'encrypted'),

    /*
    |--------------------------------------------------------------------------
    | Excluded Files
    |--------------------------------------------------------------------------
    |
    | Files or directories to exclude from encryption. These files will be
    | copied to the output directory without encryption.
    |
    */
    'excludes' => [
        // Add files or directories to exclude
        // Example: 'config/app.php',
        // Example: 'database/migrations',
    ],

    /*
    |--------------------------------------------------------------------------
    | File Extensions to Encrypt
    |--------------------------------------------------------------------------
    |
    | Only files with these extensions will be encrypted. Other files will
    | be copied without encryption.
    |
    */
    'encrypt_extensions' => ['php'],

    /*
    |--------------------------------------------------------------------------
    | Preserve Directory Structure
    |--------------------------------------------------------------------------
    |
    | Whether to preserve the directory structure when encrypting files.
    | If set to false, all encrypted files will be placed in the root
    | of the output directory.
    |
    */
    'preserve_structure' => true,
];