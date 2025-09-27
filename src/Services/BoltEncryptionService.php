<?php

namespace ZawadulKawum\LaravelBoltEncrypt\Services;

use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;
use Exception;

class BoltEncryptionService
{
    protected string $encryptionKey;
    protected array $excludes;

    public function __construct(string $encryptionKey = null, array $excludes = [])
    {
        $this->encryptionKey = $encryptionKey ?? 'kyc7fh';
        $this->excludes = $excludes;
    }

    /**
     * Encrypt a directory and its contents
     */
    public function encryptDirectory(string $sourceDir, string $outputDir): array
    {
        $results = [
            'success' => false,
            'encrypted_files' => [],
            'copied_files' => [],
            'errors' => []
        ];

        try {
            // Prepare exclude paths
            $excludePaths = [];
            foreach ($this->excludes as $file) {
                $excludePaths[] = rtrim($sourceDir, '/') . '/' . ltrim($file, '/');
            }

            $rec = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($sourceDir));

            foreach ($rec as $file) {
                if ($file->isDir()) {
                    $newDir = str_replace($sourceDir, $outputDir, $file->getPath());
                    if (!is_dir($newDir)) {
                        mkdir($newDir, 0755, true);
                    }
                    continue;
                }

                $filePath = $file->getPathname();

                // Skip excluded files
                if (in_array($filePath, $excludePaths)) {
                    continue;
                }

                $newFile = str_replace($sourceDir, $outputDir, $filePath);

                // Handle non-PHP files by copying them
                if (pathinfo($filePath, PATHINFO_EXTENSION) !== 'php') {
                    if (copy($filePath, $newFile)) {
                        $results['copied_files'][] = $filePath;
                    } else {
                        $results['errors'][] = "Failed to copy: $filePath";
                    }
                    continue;
                }

                // Encrypt PHP files
                try {
                    $this->encryptFile($filePath, $newFile);
                    $results['encrypted_files'][] = $filePath;
                } catch (Exception $e) {
                    $results['errors'][] = "Failed to encrypt $filePath: " . $e->getMessage();
                }
            }

            $results['success'] = empty($results['errors']);
            
        } catch (Exception $e) {
            $results['errors'][] = $e->getMessage();
        }

        return $results;
    }

    /**
     * Encrypt a single PHP file
     */
    public function encryptFile(string $inputFile, string $outputFile): bool
    {
        $contents = file_get_contents($inputFile);
        
        if ($contents === false) {
            throw new Exception("Cannot read file: $inputFile");
        }

        // Ensure output directory exists
        $outputDir = dirname($outputFile);
        if (!is_dir($outputDir)) {
            mkdir($outputDir, 0755, true);
        }

        // Check if the file has a namespace declaration
        $hasNamespace = preg_match('/^\s*<\?php\s*.*?namespace\s+/s', $contents);
        
        if ($hasNamespace) {
            // For files with namespace, preserve the <?php tag and namespace structure
            preg_match('/^(\s*<\?php(?:\s+.*?)?(?:declare\s*\([^)]+\)\s*;)*)/s', $contents, $matches);
            $phpTag = isset($matches[1]) ? $matches[1] : '<?php';
            
            // Remove the opening <?php tag from contents for encryption
            $contentsToEncrypt = preg_replace('/^\s*<\?php/', '', $contents);
            
            $cipher = $this->boltEncrypt($contentsToEncrypt);
            
            // Create the new file with proper structure
            $newContent = $phpTag . "\n" . 
                         'bolt_decrypt( __FILE__ , "' . $this->encryptionKey . '"); return 0;' . "\n" .
                         '##!!!##' . $cipher;
        } else {
            // For files without namespace, use the original method
            $cipher = $this->boltEncrypt("?> " . $contents);
            $newContent = '<?php bolt_decrypt( __FILE__ , "' . $this->encryptionKey . '"); return 0;' . "\n" .
                         '##!!!##' . $cipher;
        }
        
        $result = file_put_contents($outputFile, $newContent);
        
        if ($result === false) {
            throw new Exception("Cannot write to file: $outputFile");
        }

        return true;
    }

    /**
     * Bolt encryption function
     */
    protected function boltEncrypt(string $data): string
    {
        // Basic XOR encryption with the key
        $key = $this->encryptionKey;
        $keyLength = strlen($key);
        $dataLength = strlen($data);
        $encrypted = '';
        
        for ($i = 0; $i < $dataLength; $i++) {
            $encrypted .= chr(ord($data[$i]) ^ ord($key[$i % $keyLength]));
        }
        
        return base64_encode($encrypted);
    }

    /**
     * Set encryption key
     */
    public function setEncryptionKey(string $key): self
    {
        $this->encryptionKey = $key;
        return $this;
    }

    /**
     * Set exclude patterns
     */
    public function setExcludes(array $excludes): self
    {
        $this->excludes = $excludes;
        return $this;
    }

    /**
     * Get encryption key
     */
    public function getEncryptionKey(): string
    {
        return $this->encryptionKey;
    }
}