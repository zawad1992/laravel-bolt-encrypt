<?php

namespace ZawadulKawum\LaravelBoltEncrypt\Commands;

use Illuminate\Console\Command;
use ZawadulKawum\LaravelBoltEncrypt\Services\FileEncryptorService;

class EncryptFilesCommand extends Command
{
    /**
     * The name and signature of the console command.
     */
    protected $signature = 'encrypt:files 
                            {--source= : Source directory to encrypt}
                            {--destination= : Destination directory for encrypted files}
                            {--key= : Encryption key to use}';

    /**
     * The console command description.
     */
    protected $description = 'Encrypt PHP files in specified directory';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Starting file encryption process...');

        $source = $this->option('source');
        $destination = $this->option('destination');
        $key = $this->option('key');

        $encryptor = new FileEncryptorService($source, $destination, $key);

        $config = $encryptor->getConfig();
        
        $this->table(['Setting', 'Value'], [
            ['Source Directory', $config['source']],
            ['Destination Directory', $config['destination']],
            ['Encryption Key', str_repeat('*', strlen($config['key']))],
            ['Excluded Files', count($config['excludes']) . ' files'],
        ]);

        if (!$this->confirm('Do you want to proceed with encryption?')) {
            $this->info('Operation cancelled.');
            return;
        }

        $result = $encryptor->encrypt();

        if ($result['success']) {
            $this->info('✅ ' . strip_tags($result['message']));
            
            if ($result['stats']) {
                $this->table(['Metric', 'Value'], [
                    ['Total Files Processed', $result['stats']['total_files']],
                    ['PHP Files Encrypted', $result['stats']['php_files_encrypted']],
                    ['Files Copied', $result['stats']['files_copied']],
                    ['Directories Created', $result['stats']['directories_created']],
                    ['Duration', round($result['stats']['duration'], 3) . ' seconds'],
                ]);
            }
        } else {
            $this->error('❌ ' . $result['message']);
            return 1;
        }

        return 0;
    }
}