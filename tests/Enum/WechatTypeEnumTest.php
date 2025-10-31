<?php

namespace YouzanApiUserBundle\Tests\Enum;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\TestWith;
use Tourze\PHPUnitEnum\AbstractEnumTestCase;
use YouzanApiUserBundle\Enum\WechatTypeEnum;

/**
 * @internal
 */
#[CoversClass(WechatTypeEnum::class)]
final class WechatTypeEnumTest extends AbstractEnumTestCase
{
    #[TestWith([WechatTypeEnum::OFFICIAL_ACCOUNT, 1, '公众号'])]
    #[TestWith([WechatTypeEnum::MINI_PROGRAM, 2, '小程序'])]
    public function testValueAndLabel(WechatTypeEnum $enum, int $expectedValue, string $expectedLabel): void
    {
        $this->assertSame($expectedValue, $enum->value);
        $this->assertSame($expectedLabel, $enum->getLabel());
    }

    public function testValueUniqueness(): void
    {
        $values = array_map(fn (WechatTypeEnum $enum) => $enum->value, WechatTypeEnum::cases());
        $this->assertSame($values, array_unique($values));
    }

    public function testLabelUniqueness(): void
    {
        $labels = array_map(fn (WechatTypeEnum $enum) => $enum->getLabel(), WechatTypeEnum::cases());
        $this->assertSame($labels, array_unique($labels));
    }

    /**
     * 测试 fromInt 方法能正确将整数转换为枚举实例
     */
    public function testFromIntConvertsValidIntegers(): void
    {
        $this->assertSame(WechatTypeEnum::OFFICIAL_ACCOUNT, WechatTypeEnum::fromInt(1));
        $this->assertSame(WechatTypeEnum::MINI_PROGRAM, WechatTypeEnum::fromInt(2));
    }

    /**
     * 测试 fromInt 方法处理无效整数
     */
    public function testFromIntHandlesInvalidIntegers(): void
    {
        $this->assertNull(WechatTypeEnum::fromInt(0));
        $this->assertNull(WechatTypeEnum::fromInt(-1));
        $this->assertNull(WechatTypeEnum::fromInt(3));
    }

    public function testFromWithValidValue(): void
    {
        $this->assertSame(WechatTypeEnum::OFFICIAL_ACCOUNT, WechatTypeEnum::from(1));
        $this->assertSame(WechatTypeEnum::MINI_PROGRAM, WechatTypeEnum::from(2));
    }

    public function testTryFromWithValidValue(): void
    {
        $this->assertSame(WechatTypeEnum::OFFICIAL_ACCOUNT, WechatTypeEnum::tryFrom(1));
        $this->assertSame(WechatTypeEnum::MINI_PROGRAM, WechatTypeEnum::tryFrom(2));
    }

    /**
     * 测试 toArray 方法
     */
    public function testToArray(): void
    {
        $expected = [
            'value' => 1,
            'label' => '公众号',
        ];
        $this->assertSame($expected, WechatTypeEnum::OFFICIAL_ACCOUNT->toArray());
    }
}
