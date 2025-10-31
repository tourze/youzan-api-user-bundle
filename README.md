# Youzan API User Bundle

[English](README.md) | [中文](README.zh-CN.md)

[![Latest Version](https://img.shields.io/packagist/v/tourze/youzan-api-user-bundle.svg?style=flat-square)](
https://packagist.org/packages/tourze/youzan-api-user-bundle)
[![Total Downloads](https://img.shields.io/packagist/dt/tourze/youzan-api-user-bundle.svg?style=flat-square)](
https://packagist.org/packages/tourze/youzan-api-user-bundle)
[![PHP Version](https://img.shields.io/packagist/php-v/tourze/youzan-api-user-bundle.svg?style=flat-square)](
https://packagist.org/packages/tourze/youzan-api-user-bundle)
[![License](https://img.shields.io/packagist/l/tourze/youzan-api-user-bundle.svg?style=flat-square)](
https://packagist.org/packages/tourze/youzan-api-user-bundle)
[![Code Coverage](https://img.shields.io/codecov/c/github/tourze/youzan-api-user-bundle.svg?style=flat-square)](https://codecov.io/gh/tourze/youzan-api-user-bundle)

A Symfony bundle for managing Youzan API user data, providing comprehensive entities and 
synchronization commands for Youzan platform user management, including followers, staff, 
and related information.

## Table of Contents

- [Features](#features)
- [Requirements](#requirements)
- [Installation](#installation)
- [Configuration](#configuration)
- [Quick Start](#quick-start)
- [Console Commands](#console-commands)
- [Entities](#entities)
- [Usage Examples](#usage-examples)
- [Advanced Usage](#advanced-usage)
- [Security](#security)
- [Contributing](#contributing)
- [License](#license)

## Features

- **User Management**: Complete user entity with profile information including gender, 
  birthday, location, and contact details
- **Follower Synchronization**: Batch synchronization of WeChat follower data from Youzan API with progress tracking
- **Staff Management**: Comprehensive staff entity with personal and contact information
- **Level & Mobile Info**: Dedicated entities for tracking user level and mobile information
- **WeChat Integration**: Full WeChat user information management with OpenID and profile data
- **Console Commands**: Built-in commands for automated data synchronization with flexible date range options
- **Batch Processing**: Efficient batch processing for large datasets with configurable batch sizes
- **Memory Optimization**: Automatic memory management for processing large numbers of records

## Requirements

- PHP 8.1 or higher
- Symfony 6.4 or higher
- Doctrine ORM 3.0 or higher
- tourze/youzan-api-bundle for API integration

## Installation

```bash
composer require tourze/youzan-api-user-bundle
```

## Configuration

This bundle relies on the `tourze/youzan-api-bundle` for API connectivity. Ensure you have configured:

1. Youzan API credentials in your environment
2. Doctrine ORM for entity persistence
3. Proper database connections

## Quick Start

### 1. Register the Bundle

Add the bundle to your `config/bundles.php`:

```php
<?php

return [
    // ... other bundles
    YouzanApiUserBundle\YouzanApiUserBundle::class => ['all' => true],
];
```

### 2. Update Database Schema

Run the database migration to create the required tables:

```bash
php bin/console doctrine:schema:update --force
```

### 3. Configure Youzan API

Make sure you have configured the Youzan API bundle properly with your credentials.

## Console Commands

### youzan:sync:followers

Synchronizes WeChat follower information from Youzan API with support for date range filtering and batch processing.

```bash
# Sync followers for all accounts (last 7 days by default)
php bin/console youzan:sync:followers

# Sync followers for specific account
php bin/console youzan:sync:followers --account=123

# Sync followers for specific date range
php bin/console youzan:sync:followers --start-date="2024-01-01" --end-date="2024-01-31"

# Combine options for targeted synchronization
php bin/console youzan:sync:followers --account=123 --start-date="2024-01-01" --end-date="2024-01-31"
```

**Options:**
- `--account` (`-a`): Account ID (optional, defaults to all accounts)
- `--start-date` (`-s`): Start date in Y-m-d format (optional, defaults to "-7 days")
- `--end-date` (`-e`): End date in Y-m-d format (optional, defaults to "now")

The command features:
- Progress bar display for real-time tracking
- Automatic batch processing (100 records per batch)
- Memory-efficient processing with periodic entity manager clearing
- Error handling with detailed error messages per account

## Entities

### User
Main user entity representing Youzan platform users with comprehensive profile information:
- **Basic Info**: Youzan OpenID, nickname (encrypted/decrypted), gender (using GenderEnum), platform type
- **Location**: Country, province, city information
- **Profile**: Avatar URL
- **Account Association**: Links to related Youzan account
- **Relations**: Connected to Staff, WechatInfo, and MobileInfo entities
- **Timestamps**: Automatic created/updated timestamps via Doctrine traits

### Follower
WeChat follower entity synchronized from Youzan API:
- **Identification**: Youzan user ID and WeChat OpenID mapping
- **Profile**: Nickname, avatar URL, gender
- **Location Data**: Country, province, city information
- **Follow Status**: Follow state and timestamp tracking
- **Trading Statistics**: Traded count, total trade amount, points
- **Account Association**: Links to specific Youzan account

### Staff
Staff entity for managing employee information:
- **Personal Information**: Name, email
- **Company Information**: Corporate name, corporate ID, KDT ID (store ID)
- **User Association**: Links to main User entity
- **Timestamps**: Automatic created/updated timestamps via Doctrine traits

### LevelInfo
User level information tracking:
- **User Association**: Links to main User entity
- **Level Details**: Current level, experience points
- **Progression**: Level name, benefits, thresholds

### MobileInfo
Mobile information management:
- **User Association**: Links to main User entity
- **Mobile Details**: Phone number, carrier, verification status
- **Security**: Verification timestamps, binding status

### WechatInfo
WeChat-specific user data:
- **User Association**: Links to main User entity
- **WeChat Profile**: Union ID, OpenID, nickname
- **Subscription**: Subscribe status, subscribe time
- **Profile Data**: Avatar, language preference

## Usage Examples

### Basic Entity Usage

```php
use YouzanApiUserBundle\Entity\User;
use YouzanApiUserBundle\Entity\Follower;
use YouzanApiUserBundle\Enum\GenderEnum;

// Create a new user
$user = new User();
$user->setYzOpenId('youzan_user_123')
    ->setNickNameDecrypted('John Doe')
    ->setGender(GenderEnum::MALE)
    ->setCountry('China')
    ->setProvince('Guangdong')
    ->setCity('Shenzhen');

// Work with followers
$follower = new Follower();
$follower->setUserId('123456')
    ->setWeixinOpenId('wx_openid_123')
    ->setNick('微信昵称')
    ->setIsFollow(true)
    ->setFollowTime(time());
```

### Repository Usage

```php
use YouzanApiUserBundle\Repository\FollowerRepository;

// Find follower by Youzan user ID
$follower = $followerRepository->findByUserId('123456');

// Find all followers for an account
$followers = $followerRepository->findBy(['account' => $account]);

// Count active followers
$activeCount = $followerRepository->count(['isFollow' => true]);
```

## Advanced Usage

### Custom Synchronization Strategies

You can extend the synchronization process by creating custom command handlers:

```php
use YouzanApiUserBundle\Command\SyncFollowersCommand;
use YouzanApiUserBundle\Entity\Follower;

class CustomSyncFollowersCommand extends SyncFollowersCommand
{
    protected function processFollower(array $followerData, $account): ?Follower
    {
        // Custom processing logic
        $follower = parent::processFollower($followerData, $account);
        
        // Add custom business logic
        if ($follower && $this->shouldUpdateCustomFields($follower)) {
            $this->updateCustomFields($follower, $followerData);
        }
        
        return $follower;
    }
}
```

### Entity Extensions

Extend entities to add application-specific fields:

```php
use YouzanApiUserBundle\Entity\User as BaseUser;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
class ExtendedUser extends BaseUser
{
    #[ORM\Column(type: 'string', nullable: true)]
    private ?string $customField = null;
    
    // Add custom getters and setters
}
```

### Event Subscribers

Listen to synchronization events:

```php
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use YouzanApiUserBundle\Event\FollowerSyncedEvent;

class FollowerSyncSubscriber implements EventSubscriberInterface
{
    public static function getSubscribedEvents(): array
    {
        return [
            FollowerSyncedEvent::class => 'onFollowerSynced',
        ];
    }
    
    public function onFollowerSynced(FollowerSyncedEvent $event): void
    {
        // Handle post-sync logic
        $follower = $event->getFollower();
        // Custom processing...
    }
}
```

## Security

### Data Protection

This bundle handles sensitive user data and implements several security measures:

- **Encrypted Storage**: Sensitive fields like mobile numbers support both encrypted and 
  decrypted storage options
- **Input Validation**: All entity properties use Symfony validation constraints to prevent 
  malicious data injection
- **Data Sanitization**: User-generated content is properly validated before persistence

### Best Practices

1. **Environment Variables**: Store API credentials in environment variables, never in code
2. **Database Security**: Use secure database connections with SSL/TLS encryption
3. **Access Control**: Implement proper access controls for console commands in production
4. **Data Minimization**: Only synchronize necessary user data fields
5. **Audit Logging**: Consider implementing audit logs for sensitive operations

### Validation Rules

The bundle enforces strict validation rules:

```php
// Mobile numbers must match Chinese mobile format
#[Assert\Regex(pattern: '/^1[3-9]\d{9}$/', message: 'Mobile phone number must be a valid Chinese mobile number')]

// Encrypted data must be valid base64
#[Assert\Regex(pattern: '/^[a-zA-Z0-9+\/=]*$/', message: 'Encrypted data should contain only base64 characters')]
```

## Contributing

Please see [CONTRIBUTING.md](CONTRIBUTING.md) for details.

## License

The MIT License (MIT). Please see [License File](LICENSE) for more information.
