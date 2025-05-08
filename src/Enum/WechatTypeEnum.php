<?php

namespace YouzanApiUserBundle\Enum;

use Tourze\EnumExtra\Itemable;
use Tourze\EnumExtra\ItemTrait;
use Tourze\EnumExtra\Labelable;
use Tourze\EnumExtra\Selectable;
use Tourze\EnumExtra\SelectTrait;

/**
 * 微信类型枚举
 */
enum WechatTypeEnum: int implements Labelable, Itemable, Selectable
{
    use ItemTrait;
    use SelectTrait;

    case OFFICIAL_ACCOUNT = 1;
    case MINI_PROGRAM = 2;

    public function getLabel(): string
    {
        return match($this) {
            self::OFFICIAL_ACCOUNT => '公众号',
            self::MINI_PROGRAM => '小程序',
        };
    }

    /**
     * 从整数值获取枚举实例
     */
    public static function fromInt(int $value): ?self
    {
        return match($value) {
            1 => self::OFFICIAL_ACCOUNT,
            2 => self::MINI_PROGRAM,
            default => null,
        };
    }
}
