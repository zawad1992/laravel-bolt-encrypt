<?php

/**
 * Example Usage of Laravel Bolt Encrypt Package
 * 
 * This file demonstrates how to use the Laravel Bolt Encrypt package
 * both programmatically and via Artisan commands.
 */

require_once 'vendor/autoload.php';

use ZawadulKawum\LaravelBoltEncrypt\Services\BoltEncryptionService;

// Example 1: Basic encryption service usage
echo "=== Example 1: Basic Encryption Service ===\n";

$encryptionService = new BoltEncryptionService('my-secret-key');

// Encrypt a directory
$results = $encryptionService->encryptDirectory('src/app', 'encrypted');

if ($results['success']) {
    echo "✅ Encryption successful!\n";
    echo "📄 Encrypted files: " . count($results['encrypted_files']) . "\n";
    echo "📋 Copied files: " . count($results['copied_files']) . "\n";
} else {
    echo "❌ Encryption failed!\n";
    foreach ($results['errors'] as $error) {
        echo "Error: " . $error . "\n";
    }
}

echo "\n";

// Example 2: Encrypt single file
echo "=== Example 2: Single File Encryption ===\n";

try {
    $encryptionService->encryptFile('example.php', 'encrypted/example.php');
    echo "✅ Single file encrypted successfully!\n";
} catch (Exception $e) {
    echo "❌ Single file encryption failed: " . $e->getMessage() . "\n";
}

echo "\n";

// Example 3: Custom configuration
echo "=== Example 3: Custom Configuration ===\n";

$customService = new BoltEncryptionService();
$customService->setEncryptionKey('custom-key-12345')
              ->setExcludes(['config', 'tests', '*.blade.php']);

echo "🔑 Encryption key: " . $customService->getEncryptionKey() . "\n";

echo "\n";

// Example 4: Artisan Command Examples
echo "=== Example 4: Artisan Command Usage ===\n";
echo "Here are some example Artisan commands you can run:\n\n";

echo "# Basic encryption:\n";
echo "php artisan bolt:encrypt app encrypted\n\n";

echo "# With custom key:\n";
echo "php artisan bolt:encrypt app encrypted --key=my-secret-key\n\n";

echo "# Exclude specific files:\n";
echo "php artisan bolt:encrypt app encrypted --exclude=config --exclude=tests\n\n";

echo "# Verbose output:\n";
echo "php artisan bolt:encrypt app encrypted -v\n\n";

echo "=== Package Installation Commands ===\n";
echo "# Install the package:\n";
echo "composer require ZawadulKawum/laravel-bolt-encrypt\n\n";

echo "# Publish configuration:\n";
echo "php artisan vendor:publish --tag=bolt-encrypt-config\n\n";

?>