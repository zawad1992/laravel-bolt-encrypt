# Laravel Bolt Encrypt

A Laravel package for encrypting PHP files using Bolt encryption. This package provides a secure way to encrypt your Laravel application's PHP files while preserving their functionality.

## Features

- 🔐 **Secure Encryption**: Uses XOR encryption with customizable keys
- 📁 **Directory Encryption**: Encrypt entire directories while preserving structure
- 🎯 **Selective Encryption**: Exclude specific files or directories from encryption
- 🚀 **Artisan Command**: Easy-to-use command-line interface
- ⚙️ **Configurable**: Customizable settings via configuration file
- 📝 **Namespace Preservation**: Properly handles files with namespace declarations

## Installation

You can install the package via Composer:

```bash
composer require ZawadulKawum/laravel-bolt-encrypt
```

The package will automatically register its service provider.

### Publish Configuration

Publish the configuration file to customize the package settings:

```bash
php artisan vendor:publish --tag=file-encryptor-config
```

This will create a `config/file-encryptor.php` file where you can customize the package settings.

## Configuration

After publishing the configuration file, you can customize the following settings in `config/file-encryptor.php`:

```php
return [
    // Default encryption key
    'encryption_key' => env('BOLT_ENCRYPT_KEY', 'your-secret-key'),
    
    // Default source directory
    'source_path' => env('BOLT_ENCRYPT_SOURCE', 'src/app'),
    
    // Default output directory
    'output_path' => env('BOLT_ENCRYPT_OUTPUT', 'encrypted'),
    
    // Files to exclude from encryption
    'excludes' => [
        // 'config/app.php',
        // 'database/migrations',
    ],
    
    // File extensions to encrypt
    'encrypt_extensions' => ['php'],
    
    // Preserve directory structure
    'preserve_structure' => true,
];
```

### Environment Variables

You can also set these values in your `.env` file:

```env
BOLT_ENCRYPT_KEY=your-super-secret-encryption-key
BOLT_ENCRYPT_SOURCE=app
BOLT_ENCRYPT_OUTPUT=encrypted
```

## Usage

### Using the Artisan Command

The easiest way to use the package is through the provided Artisan command:

```bash
# Basic usage
php artisan bolt:encrypt app encrypted

# With custom encryption key
php artisan bolt:encrypt app encrypted --key=your-custom-key

# Exclude specific files/directories
php artisan bolt:encrypt app encrypted --exclude=config/app.php --exclude=database

# Verbose output
php artisan bolt:encrypt app encrypted -v
```

### Using the Service Class

You can also use the encryption service directly in your code:

```php
use ZawadulKawum\LaravelBoltEncrypt\Services\BoltEncryptionService;

// Create service instance
$encryptionService = new BoltEncryptionService('your-encryption-key');

// Or use dependency injection
public function encrypt(BoltEncryptionService $encryptionService)
{
    $results = $encryptionService->encryptDirectory('app', 'encrypted');
    
    if ($results['success']) {
        echo "Encrypted " . count($results['encrypted_files']) . " files!";
    } else {
        foreach ($results['errors'] as $error) {
            echo "Error: " . $error . "\n";
        }
    }
}
```

### Encrypting Individual Files

```php
use ZawadulKawum\LaravelBoltEncrypt\Services\BoltEncryptionService;

$encryptionService = new BoltEncryptionService('your-key');

try {
    $encryptionService->encryptFile('app/Models/User.php', 'encrypted/app/Models/User.php');
    echo "File encrypted successfully!";
} catch (Exception $e) {
    echo "Encryption failed: " . $e->getMessage();
}
```

## How It Works

### Encryption Process

1. **File Analysis**: The package analyzes each PHP file to detect namespace declarations
2. **Smart Encryption**: Files with namespaces preserve their structure while encrypting the content
3. **XOR Encryption**: Uses XOR cipher with your provided key for encryption
4. **Base64 Encoding**: Encrypted content is base64 encoded for safe storage
5. **Marker System**: Uses `##!!!##` marker to separate decryption code from encrypted content

### Decryption Process

The encrypted files contain a small decryption stub that:
1. Reads the encrypted content after the `##!!!##` marker
2. Decrypts the content using the same XOR algorithm
3. Executes the decrypted PHP code

### File Structure

**Original File:**
```php
<?php
namespace App\Models;

class User extends Model
{
    // Your code here
}
```

**Encrypted File:**
```php
<?php
namespace App\Models;
bolt_decrypt( __FILE__ , "your-key"); return 0;
##!!!##[BASE64_ENCRYPTED_CONTENT]
```

## Security Considerations

- **Keep Your Key Secret**: Never commit your encryption key to version control
- **Use Strong Keys**: Use long, random encryption keys for better security
- **Environment Variables**: Store encryption keys in environment variables
- **Access Control**: Ensure encrypted files have proper file permissions
- **Backup**: Always backup your original files before encryption

## Command Reference

### bolt:encrypt

Encrypt PHP files in a directory.

**Syntax:**
```bash
php artisan bolt:encrypt {source} {output} [options]
```

**Arguments:**
- `source`: Source directory to encrypt
- `output`: Output directory for encrypted files

**Options:**
- `--key=KEY`: Custom encryption key
- `--exclude=PATTERN`: Exclude files/directories (can be used multiple times)
- `-v, --verbose`: Show detailed output

**Examples:**
```bash
# Encrypt app directory to encrypted directory
php artisan bolt:encrypt app encrypted

# Use custom key
php artisan bolt:encrypt app encrypted --key=my-secret-key

# Exclude multiple patterns
php artisan bolt:encrypt app encrypted --exclude=config --exclude=*.blade.php

# Verbose output
php artisan bolt:encrypt app encrypted -v
```

## API Reference

### BoltEncryptionService

#### Constructor

```php
public function __construct(string $encryptionKey = null, array $excludes = [])
```

#### Methods

##### encryptDirectory()

```php
public function encryptDirectory(string $sourceDir, string $outputDir): array
```

Encrypt an entire directory and return results.

**Returns:**
```php
[
    'success' => bool,
    'encrypted_files' => array,
    'copied_files' => array,
    'errors' => array
]
```

##### encryptFile()

```php
public function encryptFile(string $inputFile, string $outputFile): bool
```

Encrypt a single file.

##### setEncryptionKey()

```php
public function setEncryptionKey(string $key): self
```

Set the encryption key.

##### setExcludes()

```php
public function setExcludes(array $excludes): self
```

Set files/directories to exclude from encryption.

##### getEncryptionKey()

```php
public function getEncryptionKey(): string
```

Get the current encryption key.

## Requirements

- PHP 8.0 or higher
- Laravel 9.0, 10.0, or 11.0

## Contributing

Contributions are welcome! Please feel free to submit a Pull Request.

## License

This package is open-sourced software licensed under the [MIT license](LICENSE).

## Support

If you encounter any issues or have questions, please [open an issue](https://github.com/ZawadulKawum/laravel-bolt-encrypt/issues) on GitHub.

## Changelog

### v1.0.0
- Initial release
- Basic encryption/decryption functionality
- Artisan command support
- Configuration file support
- Namespace preservation