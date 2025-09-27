# 🔐 Laravel Bolt Encrypt

[![Latest Stable Version](https://poser.pugx.org/zawaulkawum/laravel-bolt-encrypt/v/stable)](https://packagist.org/packages/zawaulkawum/laravel-bolt-encrypt)
[![Total Downloads](https://poser.pugx.org/zawaulkawum/laravel-bolt-encrypt/downloads)](https://packagist.org/packages/zawaulkawum/laravel-bolt-encrypt)
[![License](https://poser.pugx.org/zawaulkawum/laravel-bolt-encrypt/license)](https://packagist.org/packages/zawaulkawum/laravel-bolt-encrypt)
[![PHP Version Require](https://poser.pugx.org/zawaulkawum/laravel-bolt-encrypt/require/php)](https://packagist.org/packages/zawaulkawum/laravel-bolt-encrypt)

A powerful Laravel package for encrypting PHP files with namespace support, perfect for protecting your source code before deployment or distribution.

## ✨ Features

- 🚀 **One-Command Encryption** - Encrypt your entire Laravel app with a single artisan command
- 📁 **Smart Directory Handling** - Automatically processes nested directories and maintains structure
- 🔍 **Namespace Aware** - Properly handles files with and without namespaces
- ⚙️ **Highly Configurable** - Customize source, destination, encryption keys, and exclusions
- 🎯 **Selective Encryption** - Exclude specific files from encryption process
- 📊 **Detailed Statistics** - Get comprehensive reports on the encryption process
- 🛡️ **Error Handling** - Robust error handling with detailed feedback
- 🔧 **Multiple Interfaces** - CLI commands, service classes, and optional web interface

---

## 📦 Installation

### Step 1: Install via Composer

```bash
composer require zawaulkawum/laravel-bolt-encrypt
```

### Step 2: Publish Configuration (Optional)

```bash
php artisan vendor:publish --tag=file-encryptor-config
```

This will create `config/file-encryptor.php` where you can customize default settings.

### Step 3: Configure Environment Variables (Optional)

Add these to your `.env` file to override defaults:

```env
FILE_ENCRYPTOR_SOURCE=app
FILE_ENCRYPTOR_DESTINATION=encrypted
FILE_ENCRYPTOR_KEY=your-secret-key
FILE_ENCRYPTOR_WEB=false
```

---

## 🚀 Quick Start

### Encrypt Your App Directory (Default)

```bash
php artisan encrypt:files
```

This will:
- ✅ Encrypt all PHP files in the `app` directory
- ✅ Copy non-PHP files as-is
- ✅ Create encrypted files in the `encrypted` directory
- ✅ Maintain the original directory structure
- ✅ Show detailed statistics

### Sample Output:
```
Starting file encryption process...

┌─────────────────────┬──────────────┐
│ Setting             │ Value        │
├─────────────────────┼──────────────┤
│ Source Directory    │ app          │
│ Destination         │ encrypted    │
│ Encryption Key      │ ******       │
│ Excluded Files      │ 0 files      │
└─────────────────────┴──────────────┘

Do you want to proceed with encryption? (yes/no) [no]:
> yes

✅ Successfully Encrypted... Please check in /var/www/html/encrypted/app folder.

┌─────────────────────┬───────┐
│ Metric              │ Value │
├─────────────────────┼───────┤
│ Total Files         │ 45    │
│ PHP Files Encrypted │ 38    │
│ Files Copied        │ 7     │
│ Directories Created │ 12    │
│ Duration            │ 0.234 │
└─────────────────────┴───────┘
```

---

## 🎛️ Advanced Usage

### Custom Source and Destination

```bash
# Encrypt specific directory
php artisan encrypt:files --source=app/Models --destination=encrypted-models

# Encrypt with custom key
php artisan encrypt:files --key=my-super-secret-key

# Combine all options
php artisan encrypt:files --source=app/Services --destination=encrypted-services --key=custom-key
```

### Available Command Options

| Option | Description | Example |
|--------|-------------|---------|
| `--source` | Source directory to encrypt | `--source=app/Http` |
| `--destination` | Destination for encrypted files | `--destination=encrypted` |
| `--key` | Custom encryption key | `--key=my-secret-key` |

### Interactive Configuration

The command will always show you the configuration before proceeding:

```bash
php artisan encrypt:files --source=app/Http/Controllers
```

```
┌─────────────────────┬──────────────────────┐
│ Setting             │ Value                │
├─────────────────────┼──────────────────────┤
│ Source Directory    │ app/Http/Controllers │
│ Destination         │ encrypted            │
│ Encryption Key      │ ******               │
│ Excluded Files      │ 2 files              │
└─────────────────────┴──────────────────────┘

Do you want to proceed with encryption? (yes/no) [no]:
```

---

## ⚙️ Configuration

### Publishing and Editing Config

```bash
php artisan vendor:publish --tag=file-encryptor-config
```

Edit `config/file-encryptor.php`:

```php
<?php

return [
    // Default source directory
    'source_directory' => env('FILE_ENCRYPTOR_SOURCE', 'app'),
    
    // Default destination directory  
    'destination_directory' => env('FILE_ENCRYPTOR_DESTINATION', 'encrypted'),
    
    // Encryption key
    'encryption_key' => env('FILE_ENCRYPTOR_KEY', 'default-key'),
    
    // Files to exclude from encryption
    'excluded_files' => [
        'config/app.php',
        'bootstrap/app.php',
        // Add more files to exclude
    ],
    
    // Enable web interface (set to false in production)
    'enable_web_interface' => env('FILE_ENCRYPTOR_WEB', false),
];
```

### Environment Variables

| Variable | Default | Description |
|----------|---------|-------------|
| `FILE_ENCRYPTOR_SOURCE` | `app` | Source directory to encrypt |
| `FILE_ENCRYPTOR_DESTINATION` | `encrypted` | Destination directory |
| `FILE_ENCRYPTOR_KEY` | `default-key` | Encryption key |
| `FILE_ENCRYPTOR_WEB` | `false` | Enable web interface |

---

## 💻 Programmatic Usage

### Using the Service Class

```php
use YourName\FileEncryptor\Services\FileEncryptorService;

// Basic usage
$encryptor = new FileEncryptorService();
$result = $encryptor->encrypt();

if ($result['success']) {
    echo "✅ " . $result['message'];
    print_r($result['stats']);
} else {
    echo "❌ " . $result['message'];
}
```

### Custom Configuration

```php
// Custom configuration
$encryptor = new BoltEncryptService(
    'app/Models',           // source
    'encrypted-models',     // destination  
    'my-secret-key',       // encryption key
    ['User.php']           // excluded files
);

$result = $encryptor->encrypt();
```

### Fluent Interface

```php
$result = (new FileEncryptorService())
    ->setSource('app/Http/Controllers')
    ->setDestination('encrypted-controllers')
    ->setKey('controller-key')
    ->setExcludes(['BaseController.php'])
    ->encrypt();
```

### Using Laravel Container

```php
// Via service container
$result = app('file-encryptor')->encrypt();

// Via dependency injection
public function __construct(FileEncryptorService $encryptor)
{
    $this->encryptor = $encryptor;
}

public function encryptFiles()
{
    return $this->encryptor->encrypt();
}
```

---

## 🎯 Real-World Examples

### 1. Encrypt Only Models

```bash
php artisan encrypt:files --source=app/Models --destination=encrypted-models
```

### 2. Encrypt Controllers with Custom Key

```bash
php artisan encrypt:files \
  --source=app/Http/Controllers \
  --destination=encrypted-controllers \
  --key=controllers-secret-key
```

### 3. Encrypt Entire App for Production

```bash
php artisan encrypt:files \
  --source=app \
  --destination=production-encrypted \
  --key=production-encryption-key
```

### 4. Batch Processing Script

```php
<?php
// encrypt-batch.php

use YourName\FileEncryptor\Services\FileEncryptorService;

$directories = [
    'app/Models' => 'encrypted-models',
    'app/Http/Controllers' => 'encrypted-controllers', 
    'app/Services' => 'encrypted-services',
];

foreach ($directories as $source => $destination) {
    echo "Encrypting {$source}...\n";
    
    $encryptor = new FileEncryptorService($source, $destination, 'batch-key');
    $result = $encryptor->encrypt();
    
    if ($result['success']) {
        echo "✅ {$source} encrypted successfully\n";
        echo "📊 {$result['stats']['php_files_encrypted']} PHP files encrypted\n\n";
    } else {
        echo "❌ Failed to encrypt {$source}: {$result['message']}\n\n";
    }
}
```

---

## 📋 Command Reference

### Main Command

```bash
php artisan encrypt:files [options]
```

### Options

```bash
# Show help
php artisan encrypt:files --help

# Encrypt with all custom options
php artisan encrypt:files --source=app/Custom --destination=encrypted-custom --key=custom-key

# Use environment variables (set in .env first)
FILE_ENCRYPTOR_SOURCE=app/Http
FILE_ENCRYPTOR_DESTINATION=encrypted-http  
FILE_ENCRYPTOR_KEY=http-key
php artisan encrypt:files
```

### Examples with Output

**Basic encryption:**
```bash
$ php artisan encrypt:files
Starting file encryption process...
✅ Successfully Encrypted... Please check in /project/encrypted/app folder.
📊 Total Files: 23 | PHP Encrypted: 18 | Files Copied: 5 | Duration: 0.156s
```

**Custom directory:**
```bash
$ php artisan encrypt:files --source=app/Services --destination=encrypted-services
Starting file encryption process...
✅ Successfully Encrypted... Please check in /project/encrypted-services/Services folder.
📊 Total Files: 8 | PHP Encrypted: 8 | Files Copied: 0 | Duration: 0.067s
```

---

## 🔧 Troubleshooting

### Common Issues and Solutions

#### 1. "bolt_encrypt function not found"

**Problem:** The encryption function is not available.

**Solution:** Ensure you have the bolt encryption functions loaded. Add this to your bootstrap or service provider:

```php
if (!function_exists('bolt_encrypt')) {
    // Load your bolt encryption functions
    require_once base_path('bolt-functions.php');
}
```

#### 2. "Source directory does not exist"

**Problem:** The specified source directory doesn't exist.

**Solution:** Check the directory path:

```bash
# Check if directory exists
ls -la app/

# Use correct path
php artisan bolt:encrypt --source=app/Http/Controllers
```

#### 3. "Permission denied" errors

**Problem:** Insufficient permissions to create directories or files.

**Solution:** Fix permissions:

```bash
# Make sure Laravel has write permissions
sudo chown -R www-data:www-data storage/
sudo chmod -R 755 storage/

# Or for development
chmod -R 777 storage/
```

#### 4. "Failed to create directory"

**Problem:** Cannot create the destination directory.

**Solution:** Ensure parent directory exists and has write permissions:

```bash
# Create parent directory manually
mkdir -p encrypted

# Set permissions
chmod 755 encrypted/
```

### Debug Mode

Enable detailed error reporting by setting in your `.env`:

```env
APP_DEBUG=true
LOG_LEVEL=debug
```

---

## 🤝 Contributing

We welcome contributions! Here's how you can help:

### Reporting Issues

1. Check existing issues first
2. Provide detailed error messages
3. Include your Laravel and PHP versions
4. Share your configuration

### Pull Requests

1. Fork the repository
2. Create a feature branch
3. Make your changes
4. Add tests if applicable
5. Submit a pull request

### Development Setup

```bash
# Clone your fork
git clone https://github.com/zawad1992/laravel-bolt-encrypt.git

# Install dependencies
composer install

# Run tests
vendor/bin/phpunit
```

---

## 📄 License

This package is open-sourced software licensed under the [MIT license](LICENSE).

---

## 🙏 Support

If you find this package useful, please consider:

- ⭐ Starring the repository
- 🐛 Reporting issues
- 💡 Suggesting improvements
- 📖 Improving documentation

### Links

- 📦 [Packagist Package](https://packagist.org/packages/zawaulkawum/laravel-bolt-encrypt)
- 🐙 [GitHub Repository](https://github.com/zawad1992/laravel-bolt-encrypt)
- 📧 [Issues & Support](https://github.com/zawad1992/laravel-bolt-encrypt/issues)

---

## 📚 Changelog

### v1.0.0
- ✨ Initial release
- 🔐 Basic encryption functionality
- 🎛️ Artisan command interface
- ⚙️ Configuration support
- 📊 Statistics reporting

---

**Made with ❤️ by [Zawaul Kawum](https://github.com/zawad1992)**