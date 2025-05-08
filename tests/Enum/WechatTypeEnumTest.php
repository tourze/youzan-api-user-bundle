<?php

namespace YouzanApiUserBundle\Tests\Enum;

use PHPUnit\Framework\TestCase;
use YouzanApiUserBundle\Enum\WechatTypeEnum;

class WechatTypeEnumTest extends TestCase
{
    /**
     * 测试枚举的基本值
     */
    public function testEnumValues(): void
    {
        $this->assertSame(1, WechatTypeEnum::OFFICIAL_ACCOUNT->value);
        $this->assertSame(2, WechatTypeEnum::MINI_PROGRAM->value);
    }

    /**
     * 测试 getLabel 方法返回正确的标签
     */
    public function testGetLabel_returnsCorrectLabels(): void
    {
        $this->assertSame('公众号', WechatTypeEnum::OFFICIAL_ACCOUNT->getLabel());
        $this->assertSame('小程序', WechatTypeEnum::MINI_PROGRAM->getLabel());
    }

    /**
     * 测试 fromInt 方法能正确将整数转换为枚举实例
     */
    public function testFromInt_convertsValidIntegers(): void
    {
        $this->assertSame(WechatTypeEnum::OFFICIAL_ACCOUNT, WechatTypeEnum::fromInt(1));
        $this->assertSame(WechatTypeEnum::MINI_PROGRAM, WechatTypeEnum::fromInt(2));
    }

    /**
     * 测试 fromInt 方法处理无效整数
     */
    public function testFromInt_handlesInvalidIntegers(): void
    {
        $this->assertNull(WechatTypeEnum::fromInt(0));
        $this->assertNull(WechatTypeEnum::fromInt(-1));
        $this->assertNull(WechatTypeEnum::fromInt(3));
    }
}
