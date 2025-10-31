# 有赞 API 用户包

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

用于管理有赞 API 用户数据的 Symfony 包，提供完整的实体和同步命令，用于有赞平台用户管理，包括粉丝、员工和相关信息。

## 目录

- [功能特性](#功能特性)
- [系统要求](#系统要求)
- [安装](#安装)
- [配置](#配置)
- [快速开始](#快速开始)
- [控制台命令](#控制台命令)
- [实体](#实体)
- [使用示例](#使用示例)
- [高级用法](#高级用法)
- [安全性](#安全性)
- [贡献](#贡献)
- [许可证](#许可证)

## 功能特性

- **用户管理**：完整的用户实体，包含性别、生日、位置和联系方式等个人资料信息
- **粉丝同步**：批量同步有赞 API 的微信粉丝数据，支持进度跟踪
- **员工管理**：全面的员工实体，包含个人信息和联系方式
- **等级和手机信息**：专用实体跟踪用户等级和手机信息
- **微信集成**：完整的微信用户信息管理，包含 OpenID 和个人资料数据
- **控制台命令**：内置命令实现自动数据同步，支持灵活的日期范围选项
- **批量处理**：高效的批量处理大数据集，支持可配置的批次大小
- **内存优化**：自动内存管理，用于处理大量记录

## 系统要求

- PHP 8.1 或更高版本
- Symfony 6.4 或更高版本
- Doctrine ORM 3.0 或更高版本
- tourze/youzan-api-bundle 用于 API 集成

## 安装

```bash
composer require tourze/youzan-api-user-bundle
```

## 配置

此包依赖于 `tourze/youzan-api-bundle` 进行 API 连接。请确保您已配置：

1. 环境中的有赞 API 凭据
2. 用于实体持久化的 Doctrine ORM
3. 正确的数据库连接

## 快速开始

### 1. 注册包

将包添加到您的 `config/bundles.php`：

```php
<?php

return [
    // ... 其他包
    YouzanApiUserBundle\YouzanApiUserBundle::class => ['all' => true],
];
```

### 2. 更新数据库架构

运行数据库迁移以创建所需的表：

```bash
php bin/console doctrine:schema:update --force
```

### 3. 配置有赞 API

确保您已正确配置有赞 API 包及其凭据。

## 控制台命令

### youzan:sync:followers

从有赞 API 同步微信粉丝信息，支持日期范围过滤和批量处理。

```bash
# 同步所有账号的粉丝（默认最近 7 天）
php bin/console youzan:sync:followers

# 同步特定账号的粉丝
php bin/console youzan:sync:followers --account=123

# 同步特定日期范围的粉丝
php bin/console youzan:sync:followers --start-date="2024-01-01" --end-date="2024-01-31"

# 组合选项进行定向同步
php bin/console youzan:sync:followers --account=123 --start-date="2024-01-01" --end-date="2024-01-31"
```

**选项：**
- `--account` (`-a`): 账号 ID（可选，默认为所有账号）
- `--start-date` (`-s`): 开始日期，Y-m-d 格式（可选，默认为 "-7 days"）
- `--end-date` (`-e`): 结束日期，Y-m-d 格式（可选，默认为 "now"）

命令特性：
- 进度条显示，实时跟踪同步进度
- 自动批量处理（每批 100 条记录）
- 内存高效处理，定期清理实体管理器
- 错误处理，为每个账号提供详细的错误信息

## 实体

### User（用户）
主要用户实体，代表有赞平台用户，包含全面的个人资料信息：
- **基本信息**：有赞 OpenID、昵称（加密/明文）、性别（使用 GenderEnum）、平台类型
- **位置信息**：国家、省份、城市信息
- **个人资料**：头像 URL
- **账号关联**：链接到相关的有赞账号
- **关系**：连接到 Staff、WechatInfo 和 MobileInfo 实体
- **时间戳**：通过 Doctrine 特性自动创建/更新时间戳

### Follower（粉丝）
从有赞 API 同步的微信粉丝实体：
- **标识信息**：有赞用户 ID 和微信 OpenID 映射
- **个人资料**：昵称、头像 URL、性别
- **位置数据**：国家、省份、城市信息
- **关注状态**：关注状态和时间戳跟踪
- **交易统计**：交易次数、总交易金额、积分
- **账号关联**：链接到特定的有赞账号

### Staff（员工）
用于管理员工信息的实体：
- **个人信息**：姓名、邮箱
- **公司信息**：企业名称、企业 ID、KDT ID（店铺 ID）
- **用户关联**：链接到主用户实体
- **时间戳**：通过 Doctrine 特性自动创建/更新时间戳

### LevelInfo（等级信息）
用户等级信息跟踪：
- **用户关联**：链接到主用户实体
- **等级详情**：当前等级、经验值
- **进度**：等级名称、权益、阈值

### MobileInfo（手机信息）
手机信息管理：
- **用户关联**：链接到主用户实体
- **手机详情**：电话号码、运营商、验证状态
- **安全**：验证时间戳、绑定状态

### WechatInfo（微信信息）
微信特定用户数据：
- **用户关联**：链接到主用户实体
- **微信资料**：Union ID、OpenID、昵称
- **订阅**：订阅状态、订阅时间
- **资料数据**：头像、语言偏好

## 使用示例

### 基本实体使用

```php
use YouzanApiUserBundle\Entity\User;
use YouzanApiUserBundle\Entity\Follower;
use YouzanApiUserBundle\Enum\GenderEnum;

// 创建新用户
$user = new User();
$user->setYzOpenId('youzan_user_123')
    ->setNickNameDecrypted('张三')
    ->setGender(GenderEnum::MALE)
    ->setCountry('中国')
    ->setProvince('广东')
    ->setCity('深圳');

// 操作粉丝数据
$follower = new Follower();
$follower->setUserId('123456')
    ->setWeixinOpenId('wx_openid_123')
    ->setNick('微信昵称')
    ->setIsFollow(true)
    ->setFollowTime(time());
```

### 仓库使用

```php
use YouzanApiUserBundle\Repository\FollowerRepository;

// 通过有赞用户 ID 查找粉丝
$follower = $followerRepository->findByUserId('123456');

// 查找账号的所有粉丝
$followers = $followerRepository->findBy(['account' => $account]);

// 统计活跃粉丝数量
$activeCount = $followerRepository->count(['isFollow' => true]);
```

## 高级用法

### 自定义同步策略

您可以通过创建自定义命令处理器来扩展同步过程：

```php
use YouzanApiUserBundle\Command\SyncFollowersCommand;
use YouzanApiUserBundle\Entity\Follower;

class CustomSyncFollowersCommand extends SyncFollowersCommand
{
    protected function processFollower(array $followerData, $account): ?Follower
    {
        // 自定义处理逻辑
        $follower = parent::processFollower($followerData, $account);
        
        // 添加自定义业务逻辑
        if ($follower && $this->shouldUpdateCustomFields($follower)) {
            $this->updateCustomFields($follower, $followerData);
        }
        
        return $follower;
    }
}
```

### 实体扩展

扩展实体以添加应用程序特定的字段：

```php
use YouzanApiUserBundle\Entity\User as BaseUser;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
class ExtendedUser extends BaseUser
{
    #[ORM\Column(type: 'string', nullable: true)]
    private ?string $customField = null;
    
    // 添加自定义 getter 和 setter
}
```

### 事件订阅者

监听同步事件：

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
        // 处理同步后逻辑
        $follower = $event->getFollower();
        // 自定义处理...
    }
}
```

## 安全性

### 数据保护

此包处理敏感用户数据并实施了多项安全措施：

- **加密存储**：手机号等敏感字段支持加密和明文存储选项
- **输入验证**：所有实体属性都使用 Symfony 验证约束来防止恶意数据注入
- **数据清理**：用户生成的内容在持久化之前经过适当验证

### 最佳实践

1. **环境变量**：将 API 凭据存储在环境变量中，绝不在代码中硬编码
2. **数据库安全**：使用带有 SSL/TLS 加密的安全数据库连接
3. **访问控制**：在生产环境中为控制台命令实施适当的访问控制
4. **数据最小化**：仅同步必要的用户数据字段
5. **审计日志**：考虑为敏感操作实施审计日志

### 验证规则

该包强制执行严格的验证规则：

```php
// 手机号必须符合中国手机号格式
#[Assert\Regex(pattern: '/^1[3-9]\d{9}$/', message: 'Mobile phone number must be a valid Chinese mobile number')]

// 加密数据必须是有效的 base64
#[Assert\Regex(pattern: '/^[a-zA-Z0-9+\/=]*$/', message: 'Encrypted data should contain only base64 characters')]
```

## 贡献

请查看 [CONTRIBUTING.md](CONTRIBUTING.md) 了解详细信息。

## 许可证

MIT 许可证。请查看 [License File](LICENSE) 获取更多信息。
