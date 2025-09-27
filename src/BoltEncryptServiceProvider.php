<?php

namespace ZawadulKawum\LaravelBoltEncrypt;

use Illuminate\Support\ServiceProvider;
use ZawadulKawum\LaravelBoltEncrypt\Console\EncryptCommand;
use ZawadulKawum\LaravelBoltEncrypt\Services\BoltEncryptionService;

class BoltEncryptServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        // Merge package configuration
        $this->mergeConfigFrom(
            __DIR__.'/../config/bolt-encrypt.php', 'bolt-encrypt'
        );

        // Register the encryption service
        $this->app->singleton(BoltEncryptionService::class, function ($app) {
            return new BoltEncryptionService(
                config('bolt-encrypt.encryption_key'),
                config('bolt-encrypt.excludes', [])
            );
        });

        // Register the encrypt command
        $this->commands([
            EncryptCommand::class,
        ]);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Publish configuration file
        if ($this->app->runningInConsole()) {
            $this->publishes([
                __DIR__.'/../config/bolt-encrypt.php' => config_path('bolt-encrypt.php'),
            ], 'config');

            $this->publishes([
                __DIR__.'/../config/bolt-encrypt.php' => config_path('bolt-encrypt.php'),
            ], 'bolt-encrypt-config');
        }

        // Load helper functions
        if (file_exists(__DIR__.'/helpers.php')) {
            require_once __DIR__.'/helpers.php';
        }
    }

    /**
     * Get the services provided by the provider.
     */
    public function provides(): array
    {
        return [
            BoltEncryptionService::class,
            EncryptCommand::class,
        ];
    }
}