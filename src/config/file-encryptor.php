<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Default Source Directory
    |--------------------------------------------------------------------------
    |
    | This is the default source directory that will be encrypted.
    | You can override this in your .env file with FILE_ENCRYPTOR_SOURCE
    |
    */
    'source_directory' => env('FILE_ENCRYPTOR_SOURCE', 'app'),

    /*
    |--------------------------------------------------------------------------
    | Default Destination Directory  
    |--------------------------------------------------------------------------
    |
    | This is the default destination directory for encrypted files.
    | You can override this in your .env file with FILE_ENCRYPTOR_DESTINATION
    |
    */
    'destination_directory' => env('FILE_ENCRYPTOR_DESTINATION', 'encrypted'),

    /*
    |--------------------------------------------------------------------------
    | Encryption Key
    |--------------------------------------------------------------------------
    |
    | The encryption key used for encrypting files.
    | You can override this in your .env file with FILE_ENCRYPTOR_KEY
    |
    */
    'encryption_key' => env('FILE_ENCRYPTOR_KEY', 'kyc7fh'),

    /*
    |--------------------------------------------------------------------------
    | Excluded Files
    |--------------------------------------------------------------------------
    |
    | Files that should be excluded from encryption process.
    | These files will be copied as-is to the destination directory.
    |
    */
    'excluded_files' => [
        // Add files you want to exclude from encryption
        // 'config/app.php',
        // 'bootstrap/app.php',
    ],

    /*
    |--------------------------------------------------------------------------
    | Web Interface
    |--------------------------------------------------------------------------
    |
    | Enable/disable the web interface for file encryption.
    | Set to false in production for security.
    |
    */
    'enable_web_interface' => env('FILE_ENCRYPTOR_WEB', false),
];