<?php

namespace YouzanApiUserBundle\Tests\Enum;

use PHPUnit\Framework\TestCase;
use YouzanApiUserBundle\Enum\GenderEnum;

class GenderEnumTest extends TestCase
{
    /**
     * 测试枚举的基本值
     */
    public function testEnumValues(): void
    {
        $this->assertSame(0, GenderEnum::UNKNOWN->value);
        $this->assertSame(1, GenderEnum::MALE->value);
        $this->assertSame(2, GenderEnum::FEMALE->value);
    }

    /**
     * 测试 getLabel 方法返回正确的标签
     */
    public function testGetLabel_returnsCorrectLabels(): void
    {
        $this->assertSame('未知', GenderEnum::UNKNOWN->getLabel());
        $this->assertSame('男', GenderEnum::MALE->getLabel());
        $this->assertSame('女', GenderEnum::FEMALE->getLabel());
    }

    /**
     * 测试 fromInt 方法能正确将整数转换为枚举实例
     */
    public function testFromInt_convertsValidIntegers(): void
    {
        $this->assertSame(GenderEnum::UNKNOWN, GenderEnum::fromInt(0));
        $this->assertSame(GenderEnum::MALE, GenderEnum::fromInt(1));
        $this->assertSame(GenderEnum::FEMALE, GenderEnum::fromInt(2));
    }

    /**
     * 测试 fromInt 方法处理无效整数
     */
    public function testFromInt_handlesInvalidIntegers(): void
    {
        $this->assertSame(GenderEnum::UNKNOWN, GenderEnum::fromInt(-1));
        $this->assertSame(GenderEnum::UNKNOWN, GenderEnum::fromInt(99));
    }
}
