<?php

namespace ZawadulKawum\LaravelBoltEncrypt;

use Illuminate\Support\ServiceProvider;
use ZawadulKawum\LaravelBoltEncrypt\Console\Commands\BoltEncryptCommand;

class BoltEncryptServiceProvider extends ServiceProvider
{
    public function register()
    {
        $this->mergeConfigFrom(__DIR__.'/../config/bolt-encrypt.php', 'bolt-encrypt');
    }

    public function boot()
    {
        if ($this->app->runningInConsole()) {
            $this->commands([
                BoltEncryptCommand::class,
            ]);

            $this->publishes([
                __DIR__.'/../config/bolt-encrypt.php' => config_path('bolt-encrypt.php'),
            ], 'config');
        }
    }
}