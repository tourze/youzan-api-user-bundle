<?php

declare(strict_types=1);

namespace YouzanApiUserBundle\Enum;

use Tourze\EnumExtra\BadgeInterface;
use Tourze\EnumExtra\Itemable;
use Tourze\EnumExtra\ItemTrait;
use Tourze\EnumExtra\Labelable;
use Tourze\EnumExtra\Selectable;
use Tourze\EnumExtra\SelectTrait;

/**
 * 微信类型枚举
 */
enum WechatTypeEnum: int implements Labelable, Itemable, Selectable, BadgeInterface
{
    use SelectTrait;
    use ItemTrait;
    case OFFICIAL_ACCOUNT = 1;
    case MINI_PROGRAM = 2;

    public function getLabel(): string
    {
        return match ($this) {
            self::OFFICIAL_ACCOUNT => '公众号',
            self::MINI_PROGRAM => '小程序',
        };
    }

    public function getBadge(): string
    {
        return match ($this) {
            self::OFFICIAL_ACCOUNT => self::PRIMARY,
            self::MINI_PROGRAM => self::INFO,
        };
    }

    /**
     * 从整数值获取枚举实例
     */
    public static function fromInt(int $value): ?self
    {
        return match ($value) {
            1 => self::OFFICIAL_ACCOUNT,
            2 => self::MINI_PROGRAM,
            default => null,
        };
    }
}
