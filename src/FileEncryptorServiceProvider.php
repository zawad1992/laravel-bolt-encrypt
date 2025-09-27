<?php

namespace ZawadulKawum\LaravelBoltEncrypt;

use Illuminate\Support\ServiceProvider;
use ZawadulKawum\LaravelBoltEncrypt\Commands\EncryptFilesCommand;
use ZawadulKawum\LaravelBoltEncrypt\Services\FileEncryptorService;

class FileEncryptorServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        $this->mergeConfigFrom(
            __DIR__.'/config/file-encryptor.php', 'file-encryptor'
        );

        $this->app->singleton('file-encryptor', function ($app) {
            return new FileEncryptorService();
        });

        $this->app->singleton(FileEncryptorService::class, function ($app) {
            return new FileEncryptorService();
        });
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        // Publish config
        $this->publishes([
            __DIR__.'/config/file-encryptor.php' => config_path('file-encryptor.php'),
        ], 'file-encryptor-config');

        // Register commands
        if ($this->app->runningInConsole()) {
            $this->commands([
                EncryptFilesCommand::class,
            ]);
        }

        // Register routes for web interface
        $this->loadRoutesFrom(__DIR__.'/routes/web.php');
    }
}