# Changelog

All notable changes to `laravel-bolt-encrypt` will be documented in this file.

## [Unreleased]

## [1.0.0] - 2025-09-27

### Added
- Initial release of Laravel Bolt Encrypt package
- Core encryption service with XOR encryption algorithm
- Artisan command `bolt:encrypt` for command-line usage
- Configuration file support with customizable settings
- Support for namespace preservation in encrypted PHP files
- Directory-based encryption with exclude patterns
- Service provider for Laravel integration
- Comprehensive documentation and examples
- Helper functions for encryption/decryption operations

### Features
- Encrypt entire directories while preserving structure
- Exclude specific files or directories from encryption
- Custom encryption keys via configuration or command options
- Verbose output option for detailed encryption process
- Automatic handling of non-PHP files (copy without encryption)
- Base64 encoding of encrypted content for safe storage
- Error handling and reporting for failed operations

### Security
- XOR encryption with customizable keys
- Environment variable support for sensitive configuration
- Proper file permission handling
- Secure key management recommendations