# Laravel Bolt Encrypt

A Laravel package for encrypting PHP files using Bolt encryption.

## Installation

1. Install via Composer:
```bash
composer require yourvendor/laravel-bolt-encrypt
```

2. Publish the configuration file:
```bash
php artisan vendor:publish --provider="ZawadulKawum\LaravelBoltEncrypt\BoltEncryptServiceProvider" --tag="config"
```

## Usage

### Basic Usage
Encrypt the entire `app` directory:
```bash
php artisan bolt:encrypt
```

### Encrypt Specific Directory
```bash
php artisan bolt:encrypt app/Models
```

### Custom Output Directory
```bash
php artisan bolt:encrypt app --output=my-encrypted-files
```

### Custom Encryption Key
```bash
php artisan bolt:encrypt app --key=mySecretKey
```

### Exclude Files/Directories
```bash
php artisan bolt:encrypt app --exclude=Models --exclude=Controllers/TestController.php
```

## Configuration

Edit `config/bolt-encrypt.php` to set default values:

- `key`: Default encryption key
- `excludes`: Files/directories to exclude by default
- `output`: Default output directory

## Environment Variables

Set your encryption key in `.env`:
```
BOLT_ENCRYPT_KEY=your-secret-key-here
```

## Requirements

- PHP ^8.0
- Laravel ^8.0|^9.0|^10.0|^11.0

## License

MIT License