<?php

namespace ZawadulKawum\LaravelBoltEncrypt\Services;

use RecursiveIteratorIterator;
use RecursiveDirectoryIterator;
use Exception;

class FileEncryptorService
{
    /**
     * Source directory
     */
    private $src;

    /**
     * Destination directory  
     */
    private $destination;

    /**
     * Encryption key
     */
    private $php_blot_key;

    /**
     * Excluded files
     */
    private $excludes = [];

    /**
     * Required functions array
     */
    private $require_funcs = ['include_once', 'include', 'require', 'require_once'];

    /**
     * Constructor
     */
    public function __construct($src = null, $destination = null, $key = null, $excludes = [])
    {
        $this->src = $src ?? config('file-encryptor.source_directory', 'app');
        $this->destination = $destination ?? config('file-encryptor.destination_directory', 'encrypted');
        $this->php_blot_key = $key ?? config('file-encryptor.encryption_key', 'kyc7fh');
        $this->excludes = $excludes ?: config('file-encryptor.excluded_files', []);
        
        // Convert relative excludes to absolute paths
        foreach($this->excludes as $key => $file) {
            $this->excludes[$key] = $this->src.'/'.$file;
        }
    }

    /**
     * Set source directory
     */
    public function setSource($src)
    {
        $this->src = $src;
        return $this;
    }

    /**
     * Set destination directory
     */
    public function setDestination($destination)
    {
        $this->destination = $destination;
        return $this;
    }

    /**
     * Set encryption key
     */
    public function setKey($key)
    {
        $this->php_blot_key = $key;
        return $this;
    }

    /**
     * Set excluded files
     */
    public function setExcludes($excludes)
    {
        $this->excludes = [];
        foreach($excludes as $key => $file) {
            $this->excludes[$key] = $this->src.'/'.$file;
        }
        return $this;
    }

    /**
     * Main encryption method - keeping original index.php logic
     */
    public function encrypt()
    {
        try {
            // Validate source directory exists
            if (!is_dir($this->src)) {
                throw new Exception("Source directory '{$this->src}' does not exist.");
            }

            // Check if bolt_encrypt function exists
            if (!function_exists('bolt_encrypt')) {
                throw new Exception("bolt_encrypt function is not available. Please ensure encryption functions are loaded.");
            }

            $rec = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($this->src));
            $stats = [
                'total_files' => 0,
                'php_files_encrypted' => 0,
                'files_copied' => 0,
                'directories_created' => 0,
                'start_time' => microtime(true)
            ];

            foreach ($rec as $file) {
                if ($file->isDir()) {
                    $newDir = str_replace($this->src, $this->destination, $file->getPath());
                    if (!is_dir($newDir)) {
                        if (!mkdir($newDir, 0755, true)) {
                            throw new Exception("Failed to create directory: {$newDir}");
                        }
                        $stats['directories_created']++;
                    }
                    continue;
                }

                $filePath = $file->getPathname();

                // Handle non-PHP files or excluded files - copy them as-is
                if (pathinfo($filePath, PATHINFO_EXTENSION) != 'php' || 
                    in_array($filePath, $this->excludes)) {
                    
                    $newFile = str_replace($this->src, $this->destination, $filePath);
                    $newDir = dirname($newFile);
                    
                    if (!is_dir($newDir)) {
                        mkdir($newDir, 0755, true);
                    }
                    
                    if (!copy($filePath, $newFile)) {
                        throw new Exception("Failed to copy file: {$filePath}");
                    }
                    
                    $stats['files_copied']++;
                    $stats['total_files']++;
                    continue;
                }

                // Handle PHP files - encrypt them (original logic preserved)
                $contents = file_get_contents($filePath);
                if ($contents === false) {
                    throw new Exception("Failed to read file: {$filePath}");
                }

                // Check if the file has a namespace declaration
                $hasNamespace = preg_match('/^\s*<\?php\s*.*?namespace\s+/s', $contents);

                if ($hasNamespace) {
                    // For files with namespace, preserve the <?php tag and namespace structure
                    preg_match('/^(\s*<\?php(?:\s+.*?)?(?:declare\s*\([^)]+\)\s*;)*)/s', $contents, $matches);
                    $phpTag = isset($matches[1]) ? $matches[1] : '<?php';
                    
                    // Remove the opening <?php tag from contents for encryption
                    $contentsToEncrypt = preg_replace('/^\s*<\?php/', '', $contents);
                    
                    $cipher = bolt_encrypt($contentsToEncrypt, $this->php_blot_key);
                    
                    // Create the new file with proper structure
                    $newContent = $phpTag . "\n" . 
                                 'bolt_decrypt( __FILE__ , "' . $this->php_blot_key . '"); return 0;' . "\n" .
                                 '##!!!##' . $cipher;
                } else {
                    // For files without namespace, use the original method
                    $cipher = bolt_encrypt("?> ".$contents, $this->php_blot_key);
                    $newContent = '<?php bolt_decrypt( __FILE__ , "'.$this->php_blot_key.'"); return 0;' . "\n" .
                                 '##!!!##' . $cipher;
                }

                $newFile = str_replace($this->src, $this->destination, $filePath);
                $newDir = dirname($newFile);
                
                if (!is_dir($newDir)) {
                    mkdir($newDir, 0755, true);
                }
                
                $fp = fopen($newFile, 'w');
                if ($fp === false) {
                    throw new Exception("Failed to open file for writing: {$newFile}");
                }
                
                if (fwrite($fp, $newContent) === false) {
                    fclose($fp);
                    throw new Exception("Failed to write to file: {$newFile}");
                }
                
                fclose($fp);

                // Clean up memory
                unset($cipher);
                unset($contents);
                unset($newContent);

                $stats['php_files_encrypted']++;
                $stats['total_files']++;
            }

            $stats['end_time'] = microtime(true);
            $stats['duration'] = $stats['end_time'] - $stats['start_time'];

            return [
                'success' => true,
                'message' => $this->getSuccessMessage(),
                'stats' => $stats
            ];

        } catch (Exception $e) {
            return [
                'success' => false,
                'message' => 'Encryption failed: ' . $e->getMessage(),
                'stats' => null
            ];
        }
    }

    /**
     * Get success message - keeping original format
     */
    private function getSuccessMessage()
    {
        $out_str = substr_replace($this->src, '', 0, strlen($this->src) - strlen(basename($this->src)));
        $file_location = base_path($this->destination . "/" . basename($this->src));
        
        return "Successfully Encrypted... Please check in <b>" . $file_location . "</b> folder.";
    }

    /**
     * Get current configuration
     */
    public function getConfig()
    {
        return [
            'source' => $this->src,
            'destination' => $this->destination,
            'key' => $this->php_blot_key,
            'excludes' => $this->excludes
        ];
    }
}