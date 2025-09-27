<?php

use Illuminate\Support\Facades\Route;
use ZawadulKawum\LaravelBoltEncrypt\Services\FileEncryptorService;

// Only register routes if web interface is enabled
if (config('file-encryptor.enable_web_interface', false)) {
    
    Route::get('/encrypt-files', function() {
        return view('file-encryptor::encrypt-form');
    })->name('file-encryptor.form');

    Route::post('/encrypt-files', function() {
        $source = request('source', config('file-encryptor.source_directory'));
        $destination = request('destination', config('file-encryptor.destination_directory'));
        $key = request('key', config('file-encryptor.encryption_key'));

        $encryptor = new FileEncryptorService($source, $destination, $key);
        $result = $encryptor->encrypt();

        return response()->json($result);
    })->name('file-encryptor.encrypt');
}