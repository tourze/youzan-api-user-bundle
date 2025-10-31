<?php

namespace YouzanApiUserBundle\Tests\Enum;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\TestWith;
use Tourze\PHPUnitEnum\AbstractEnumTestCase;
use YouzanApiUserBundle\Enum\GenderEnum;

/**
 * @internal
 */
#[CoversClass(GenderEnum::class)]
final class GenderEnumTest extends AbstractEnumTestCase
{
    #[TestWith([GenderEnum::UNKNOWN, 0, '未知'])]
    #[TestWith([GenderEnum::MALE, 1, '男'])]
    #[TestWith([GenderEnum::FEMALE, 2, '女'])]
    public function testValueAndLabel(GenderEnum $enum, int $expectedValue, string $expectedLabel): void
    {
        $this->assertSame($expectedValue, $enum->value);
        $this->assertSame($expectedLabel, $enum->getLabel());
    }

    public function testValueUniqueness(): void
    {
        $values = array_map(fn (GenderEnum $enum) => $enum->value, GenderEnum::cases());
        $this->assertSame($values, array_unique($values));
    }

    public function testLabelUniqueness(): void
    {
        $labels = array_map(fn (GenderEnum $enum) => $enum->getLabel(), GenderEnum::cases());
        $this->assertSame($labels, array_unique($labels));
    }

    /**
     * 测试 fromInt 方法能正确将整数转换为枚举实例
     */
    public function testFromIntConvertsValidIntegers(): void
    {
        $this->assertSame(GenderEnum::UNKNOWN, GenderEnum::fromInt(0));
        $this->assertSame(GenderEnum::MALE, GenderEnum::fromInt(1));
        $this->assertSame(GenderEnum::FEMALE, GenderEnum::fromInt(2));
    }

    /**
     * 测试 fromInt 方法处理无效整数
     */
    public function testFromIntHandlesInvalidIntegers(): void
    {
        $this->assertSame(GenderEnum::UNKNOWN, GenderEnum::fromInt(-1));
        $this->assertSame(GenderEnum::UNKNOWN, GenderEnum::fromInt(99));
    }

    public function testFromWithValidValue(): void
    {
        $this->assertSame(GenderEnum::UNKNOWN, GenderEnum::from(0));
        $this->assertSame(GenderEnum::MALE, GenderEnum::from(1));
        $this->assertSame(GenderEnum::FEMALE, GenderEnum::from(2));
    }

    public function testTryFromWithValidValue(): void
    {
        $this->assertSame(GenderEnum::UNKNOWN, GenderEnum::tryFrom(0));
        $this->assertSame(GenderEnum::MALE, GenderEnum::tryFrom(1));
        $this->assertSame(GenderEnum::FEMALE, GenderEnum::tryFrom(2));
    }

    /**
     * 测试 toArray 方法
     */
    public function testToArray(): void
    {
        $expected = [
            'value' => 0,
            'label' => '未知',
        ];
        $this->assertSame($expected, GenderEnum::UNKNOWN->toArray());
    }
}
