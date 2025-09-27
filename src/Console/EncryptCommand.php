<?php

namespace ZawadulKawum\LaravelBoltEncrypt\Console;

use Illuminate\Console\Command;
use ZawadulKawum\LaravelBoltEncrypt\Services\BoltEncryptionService;

class EncryptCommand extends Command
{
    /**
     * The name and signature of the console command.
     */
    protected $signature = 'bolt:encrypt 
                            {source : Source directory to encrypt}
                            {output : Output directory for encrypted files}
                            {--key= : Encryption key (optional)}
                            {--exclude=* : Files/directories to exclude from encryption}';

    /**
     * The console command description.
     */
    protected $description = 'Encrypt PHP files using Bolt encryption';

    /**
     * Execute the console command.
     */
    public function handle(BoltEncryptionService $encryptionService): int
    {
        $source = $this->argument('source');
        $output = $this->argument('output');
        $key = $this->option('key');
        $excludes = $this->option('exclude') ?? [];

        // Validate source directory
        if (!is_dir($source)) {
            $this->error("Source directory does not exist: {$source}");
            return 1;
        }

        // Set encryption key if provided
        if ($key) {
            $encryptionService->setEncryptionKey($key);
        }

        // Set excludes if provided
        if (!empty($excludes)) {
            $encryptionService->setExcludes($excludes);
        }

        $this->info("Starting encryption process...");
        $this->info("Source: {$source}");
        $this->info("Output: {$output}");
        $this->info("Encryption Key: " . $encryptionService->getEncryptionKey());

        if (!empty($excludes)) {
            $this->info("Excludes: " . implode(', ', $excludes));
        }

        $this->newLine();

        // Start encryption
        $results = $encryptionService->encryptDirectory($source, $output);

        // Display results
        if ($results['success']) {
            $this->info("✅ Encryption completed successfully!");
            
            if (!empty($results['encrypted_files'])) {
                $this->info("📄 Encrypted files: " . count($results['encrypted_files']));
                if ($this->getOutput()->isVerbose()) {
                    foreach ($results['encrypted_files'] as $file) {
                        $this->line("  - {$file}");
                    }
                }
            }

            if (!empty($results['copied_files'])) {
                $this->info("📋 Copied files: " . count($results['copied_files']));
                if ($this->getOutput()->isVerbose()) {
                    foreach ($results['copied_files'] as $file) {
                        $this->line("  - {$file}");
                    }
                }
            }

            $this->newLine();
            $this->info("🎉 Successfully encrypted! Please check the '{$output}' folder.");
            
            return 0;
        } else {
            $this->error("❌ Encryption failed!");
            
            if (!empty($results['errors'])) {
                $this->error("Errors encountered:");
                foreach ($results['errors'] as $error) {
                    $this->error("  - {$error}");
                }
            }
            
            return 1;
        }
    }
}