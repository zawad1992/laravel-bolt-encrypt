<?php

namespace ZawadulKawum\LaravelBoltEncrypt\Services;

use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;
use Exception;

class BoltEncryptService
{
    /**
     * Encrypt files in the given directory
     */
    public function encrypt(string $sourcePath, string $outputDir, string $key, array $excludes = []): array
    {
        // Handle output path - use base_path if available (Laravel), otherwise use relative path
        if (function_exists('base_path')) {
            $outputPath = call_user_func('base_path', $outputDir);
        } else {
            $outputPath = $outputDir;
        }
        
        // Create output directory if it doesn't exist
        if (!is_dir($outputPath)) {
            mkdir($outputPath, 0755, true);
        }

        // Create the decrypt function file
        $this->createDecryptFunction($outputPath, $key);

        // Prepare exclude list with full paths
        $excludeList = [];
        foreach ($excludes as $exclude) {
            $excludeList[] = $sourcePath . '/' . ltrim($exclude, '/');
        }

        $iterator = new RecursiveIteratorIterator(
            new RecursiveDirectoryIterator($sourcePath, RecursiveDirectoryIterator::SKIP_DOTS)
        );

        $processed = 0;
        $skipped = 0;

        foreach ($iterator as $file) {
            $filePath = $file->getPathname();
            $relativePath = str_replace($sourcePath, '', $filePath);
            $newFilePath = $outputPath . $relativePath;

            // Skip excluded files
            if (in_array($filePath, $excludeList)) {
                continue;
            }

            // Create directory structure
            if ($file->isDir()) {
                if (!is_dir($newFilePath)) {
                    mkdir($newFilePath, 0755, true);
                }
                continue;
            }

            // Handle files
            if (pathinfo($filePath, PATHINFO_EXTENSION) === 'php') {
                $this->encryptPhpFile($filePath, $newFilePath, $key, $sourcePath, $outputPath);
                $processed++;
            } else {
                // Copy non-PHP files as-is
                $this->ensureDirectoryExists(dirname($newFilePath));
                copy($filePath, $newFilePath);
                $skipped++;
            }
        }

        return [
            'processed' => $processed,
            'skipped' => $skipped
        ];
    }

    /**
     * Encrypt a single PHP file while preserving namespaces and structure
     */
    protected function encryptPhpFile(string $sourceFilePath, string $outputFilePath, string $key, string $sourceRootDir = '', string $outputRootDir = ''): void
    {
        $contents = file_get_contents($sourceFilePath);
        if ($contents === false) {
            throw new Exception("Could not read file: {$sourceFilePath}");
        }

        // Preserve the original file structure by encrypting the entire content
        // This maintains namespaces, use statements, and class declarations intact
        // Following the original script approach: encrypt with "?> " prefix
        $cipher = $this->boltEncrypt("?> " . $contents, $key);
        
        // Calculate relative path from current file to the bolt_decrypt.php in output root
        $currentFileDir = dirname($outputFilePath);
        $relativePath = $this->calculateRelativePath($currentFileDir, $outputRootDir);
        
        // Include the decrypt function and then decrypt this specific file (matching original script format)  
        $template = '<?php require_once __DIR__ . "%s/bolt_decrypt.php"; bolt_decrypt( __FILE__ , "%s"); return 0;' . PHP_EOL . '##!!!##';
        $prepend = sprintf($template, $relativePath, $key);
        
        $this->ensureDirectoryExists(dirname($outputFilePath));
        
        if (file_put_contents($outputFilePath, $prepend . $cipher) === false) {
            throw new Exception("Could not write file: {$outputFilePath}");
        }
    }

    /**
     * Bolt encryption function
     */
    protected function boltEncrypt(string $data, string $key): string
    {
        // This is a simple XOR encryption similar to bolt encrypt
        // Replace this with your actual bolt_encrypt implementation
        $result = '';
        $keyLen = strlen($key);
        $dataLen = strlen($data);
        
        for ($i = 0; $i < $dataLen; $i++) {
            $result .= chr(ord($data[$i]) ^ ord($key[$i % $keyLen]));
        }
        
        return base64_encode($result);
    }

    /**
     * Create the bolt_decrypt function file that will be included
     */
    public function createDecryptFunction(string $outputPath, string $key): void
    {
        $decryptFunctionContent = '<?php
if (!function_exists(\'bolt_decrypt\')) {
    function bolt_decrypt($file, $key) {
        static $cache = [];
        
        if (isset($cache[$file])) {
            return;
        }
        
        $content = file_get_contents($file);
        $delimiter = "##!!!##";
        $pos = strpos($content, $delimiter);
        
        if ($pos === false) {
            throw new Exception("Invalid encrypted file format");
        }
        
        $encrypted = substr($content, $pos + strlen($delimiter));
        $decrypted = bolt_decrypt_data($encrypted, $key);
        
        $cache[$file] = true;
        eval($decrypted);
    }
    
    function bolt_decrypt_data($encrypted, $key) {
        $data = base64_decode($encrypted);
        $result = "";
        $keyLen = strlen($key);
        $dataLen = strlen($data);
        
        for ($i = 0; $i < $dataLen; $i++) {
            $result .= chr(ord($data[$i]) ^ ord($key[$i % $keyLen]));
        }
        
        return $result;
    }
}

// Global bolt_encrypt function to match original script
if (!function_exists(\'bolt_encrypt\')) {
    function bolt_encrypt($data, $key) {
        $result = "";
        $keyLen = strlen($key);
        $dataLen = strlen($data);
        
        for ($i = 0; $i < $dataLen; $i++) {
            $result .= chr(ord($data[$i]) ^ ord($key[$i % $keyLen]));
        }
        
        return base64_encode($result);
    }
}
?>';
        
        $decryptFile = $outputPath . '/bolt_decrypt.php';
        file_put_contents($decryptFile, $decryptFunctionContent);
    }

    /**
     * Calculate relative path from one directory to another
     */
    protected function calculateRelativePath(string $from, string $to): string
    {
        $from = rtrim(str_replace('\\', '/', realpath($from)), '/');
        $to = rtrim(str_replace('\\', '/', realpath($to)), '/');
        
        $fromParts = explode('/', $from);
        $toParts = explode('/', $to);
        
        // Find common path
        $commonLength = 0;
        for ($i = 0; $i < min(count($fromParts), count($toParts)); $i++) {
            if ($fromParts[$i] === $toParts[$i]) {
                $commonLength++;
            } else {
                break;
            }
        }
        
        // Build relative path
        $relativePath = '';
        
        // Go up from current directory
        $upSteps = count($fromParts) - $commonLength;
        if ($upSteps > 0) {
            $relativePath = str_repeat('../', $upSteps);
        }
        
        // Add path down to target directory
        if ($commonLength < count($toParts)) {
            $relativePath .= implode('/', array_slice($toParts, $commonLength));
        }
        
        return $relativePath ? '/' . rtrim($relativePath, '/') : '';
    }

    /**
     * Ensure directory exists
     */
    protected function ensureDirectoryExists(string $directory): void
    {
        if (!is_dir($directory)) {
            mkdir($directory, 0755, true);
        }
    }
}