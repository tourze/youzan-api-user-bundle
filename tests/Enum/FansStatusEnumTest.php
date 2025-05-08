<?php

namespace YouzanApiUserBundle\Tests\Enum;

use PHPUnit\Framework\TestCase;
use YouzanApiUserBundle\Enum\FansStatusEnum;

class FansStatusEnumTest extends TestCase
{
    /**
     * 测试枚举的基本值
     */
    public function testEnumValues(): void
    {
        $this->assertSame(0, FansStatusEnum::UNFOLLOWED->value);
        $this->assertSame(1, FansStatusEnum::FOLLOWED->value);
        $this->assertSame(2, FansStatusEnum::SILENT_AUTH->value);
    }

    /**
     * 测试 getLabel 方法返回正确的标签
     */
    public function testGetLabel_returnsCorrectLabels(): void
    {
        $this->assertSame('已取关', FansStatusEnum::UNFOLLOWED->getLabel());
        $this->assertSame('已关注', FansStatusEnum::FOLLOWED->getLabel());
        $this->assertSame('静默授权', FansStatusEnum::SILENT_AUTH->getLabel());
    }

    /**
     * 测试 fromInt 方法能正确将整数转换为枚举实例
     */
    public function testFromInt_convertsValidIntegers(): void
    {
        $this->assertSame(FansStatusEnum::UNFOLLOWED, FansStatusEnum::fromInt(0));
        $this->assertSame(FansStatusEnum::FOLLOWED, FansStatusEnum::fromInt(1));
        $this->assertSame(FansStatusEnum::SILENT_AUTH, FansStatusEnum::fromInt(2));
    }

    /**
     * 测试 fromInt 方法处理无效整数
     */
    public function testFromInt_handlesInvalidIntegers(): void
    {
        $this->assertNull(FansStatusEnum::fromInt(-1));
        $this->assertNull(FansStatusEnum::fromInt(99));
    }

    /**
     * 测试 isFans 方法
     */
    public function testIsFans_identifiesFollowers(): void
    {
        $this->assertFalse(FansStatusEnum::UNFOLLOWED->isFans());
        $this->assertTrue(FansStatusEnum::FOLLOWED->isFans());
        $this->assertFalse(FansStatusEnum::SILENT_AUTH->isFans());
    }
}
