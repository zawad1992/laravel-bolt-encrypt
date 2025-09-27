<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Default Encryption Key
    |--------------------------------------------------------------------------
    |
    | This is the default encryption key used for bolt encryption.
    | You can override this with the --key option when running the command.
    |
    */
    'key' => env('BOLT_ENCRYPT_KEY', 'kyc7fh'),

    /*
    |--------------------------------------------------------------------------
    | Default Excludes
    |--------------------------------------------------------------------------
    |
    | Files and directories to exclude from encryption by default.
    | Paths are relative to the source directory.
    |
    */
    'excludes' => [
        '.git',
        '.gitignore',
        'composer.json',
        'composer.lock',
        'package.json',
        'package-lock.json',
        'node_modules',
        'vendor',
    ],

    /*
    |--------------------------------------------------------------------------
    | Default Output Directory
    |--------------------------------------------------------------------------
    |
    | The default directory where encrypted files will be saved.
    |
    */
    'output' => 'encrypted',
];
