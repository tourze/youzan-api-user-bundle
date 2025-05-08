<?php

namespace YouzanApiUserBundle\Enum;

use Tourze\EnumExtra\Itemable;
use Tourze\EnumExtra\ItemTrait;
use Tourze\EnumExtra\Labelable;
use Tourze\EnumExtra\Selectable;
use Tourze\EnumExtra\SelectTrait;

/**
 * 性别枚举
 */
enum GenderEnum: int implements Labelable, Itemable, Selectable
{
    use ItemTrait;
    use SelectTrait;

    case UNKNOWN = 0;
    case MALE = 1;
    case FEMALE = 2;

    public function getLabel(): string
    {
        return match($this) {
            self::UNKNOWN => '未知',
            self::MALE => '男',
            self::FEMALE => '女',
        };
    }

    /**
     * 从整数值获取枚举实例
     */
    public static function fromInt(int $value): self
    {
        return match($value) {
            0 => self::UNKNOWN,
            1 => self::MALE,
            2 => self::FEMALE,
            default => self::UNKNOWN,
        };
    }
}
