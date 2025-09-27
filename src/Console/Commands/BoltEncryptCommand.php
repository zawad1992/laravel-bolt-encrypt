<?php

namespace ZawadulKawum\LaravelBoltEncrypt\Console\Commands;

use Illuminate\Console\Command;
use ZawadulKawum\LaravelBoltEncrypt\Services\BoltEncryptService;

class BoltEncryptCommand extends Command
{
    protected $signature = 'bolt:encrypt 
                           {source? : Source directory to encrypt (default: app)} 
                           {--output= : Output directory (default: encrypted)} 
                           {--key= : Encryption key (default: from config)} 
                           {--exclude=* : Files/directories to exclude}';

    protected $description = 'Encrypt PHP files using Bolt encryption';

    protected $encryptService;

    public function __construct(BoltEncryptService $encryptService)
    {
        parent::__construct();
        $this->encryptService = $encryptService;
    }

    public function handle()
    {
        $source = $this->argument('source') ?? 'app';
        $output = $this->option('output') ?? 'encrypted';
        $key = $this->option('key') ?? config('bolt-encrypt.key');
        $excludes = $this->option('exclude') ?? config('bolt-encrypt.excludes', []);

        // Validate source directory
        $sourcePath = base_path($source);
        if (!is_dir($sourcePath)) {
            $this->error("Source directory '{$source}' does not exist.");
            return Command::FAILURE;
        }

        // Validate encryption key
        if (empty($key)) {
            $this->error('Encryption key is required. Set it in config or use --key option.');
            return Command::FAILURE;
        }

        $this->info("Starting encryption process...");
        $this->info("Source: {$sourcePath}");
        $this->info("Output: " . base_path($output));
        $this->info("Key: " . str_repeat('*', strlen($key)));

        try {
            $result = $this->encryptService->encrypt($sourcePath, $output, $key, $excludes);
            
            $this->info("✅ Successfully encrypted {$result['processed']} files!");
            $this->info("📁 Encrypted files saved to: " . base_path($output));
            
            if ($result['skipped'] > 0) {
                $this->warn("⚠️  Skipped {$result['skipped']} non-PHP files.");
            }

            return Command::SUCCESS;
        } catch (\Exception $e) {
            $this->error("❌ Encryption failed: " . $e->getMessage());
            return Command::FAILURE;
        }
    }
}