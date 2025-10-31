<?php

namespace YouzanApiUserBundle\Enum;

use Tourze\EnumExtra\BadgeInterface;
use Tourze\EnumExtra\Itemable;
use Tourze\EnumExtra\ItemTrait;
use Tourze\EnumExtra\Labelable;
use Tourze\EnumExtra\Selectable;
use Tourze\EnumExtra\SelectTrait;

/**
 * 粉丝状态枚举
 */
enum FansStatusEnum: int implements Labelable, Itemable, Selectable, BadgeInterface
{
    use SelectTrait;
    use ItemTrait;
    case UNFOLLOWED = 0;
    case FOLLOWED = 1;
    case SILENT_AUTH = 2;

    public function getLabel(): string
    {
        return match ($this) {
            self::UNFOLLOWED => '已取关',
            self::FOLLOWED => '已关注',
            self::SILENT_AUTH => '静默授权',
        };
    }

    public function getBadge(): string
    {
        return match ($this) {
            self::UNFOLLOWED => self::DANGER,
            self::FOLLOWED => self::SUCCESS,
            self::SILENT_AUTH => self::INFO,
        };
    }

    /**
     * 从整数值获取枚举实例
     */
    public static function fromInt(int $value): ?self
    {
        return match ($value) {
            0 => self::UNFOLLOWED,
            1 => self::FOLLOWED,
            2 => self::SILENT_AUTH,
            default => null,
        };
    }

    /**
     * 判断是否为粉丝（已关注）
     */
    public function isFans(): bool
    {
        return self::FOLLOWED === $this;
    }
}
