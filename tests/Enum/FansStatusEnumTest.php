<?php

namespace YouzanApiUserBundle\Tests\Enum;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\TestWith;
use Tourze\PHPUnitEnum\AbstractEnumTestCase;
use YouzanApiUserBundle\Enum\FansStatusEnum;

/**
 * @internal
 */
#[CoversClass(FansStatusEnum::class)]
final class FansStatusEnumTest extends AbstractEnumTestCase
{
    #[TestWith([FansStatusEnum::UNFOLLOWED, 0, '已取关'])]
    #[TestWith([FansStatusEnum::FOLLOWED, 1, '已关注'])]
    #[TestWith([FansStatusEnum::SILENT_AUTH, 2, '静默授权'])]
    public function testValueAndLabel(FansStatusEnum $enum, int $expectedValue, string $expectedLabel): void
    {
        $this->assertSame($expectedValue, $enum->value);
        $this->assertSame($expectedLabel, $enum->getLabel());
    }

    public function testValueUniqueness(): void
    {
        $values = array_map(fn (FansStatusEnum $enum) => $enum->value, FansStatusEnum::cases());
        $this->assertSame($values, array_unique($values));
    }

    public function testLabelUniqueness(): void
    {
        $labels = array_map(fn (FansStatusEnum $enum) => $enum->getLabel(), FansStatusEnum::cases());
        $this->assertSame($labels, array_unique($labels));
    }

    /**
     * 测试 fromInt 方法能正确将整数转换为枚举实例
     */
    public function testFromIntConvertsValidIntegers(): void
    {
        $this->assertSame(FansStatusEnum::UNFOLLOWED, FansStatusEnum::fromInt(0));
        $this->assertSame(FansStatusEnum::FOLLOWED, FansStatusEnum::fromInt(1));
        $this->assertSame(FansStatusEnum::SILENT_AUTH, FansStatusEnum::fromInt(2));
    }

    /**
     * 测试 fromInt 方法处理无效整数
     */
    public function testFromIntHandlesInvalidIntegers(): void
    {
        $this->assertNull(FansStatusEnum::fromInt(-1));
        $this->assertNull(FansStatusEnum::fromInt(99));
    }

    public function testFromWithValidValue(): void
    {
        $this->assertSame(FansStatusEnum::UNFOLLOWED, FansStatusEnum::from(0));
        $this->assertSame(FansStatusEnum::FOLLOWED, FansStatusEnum::from(1));
        $this->assertSame(FansStatusEnum::SILENT_AUTH, FansStatusEnum::from(2));
    }

    public function testTryFromWithValidValue(): void
    {
        $this->assertSame(FansStatusEnum::UNFOLLOWED, FansStatusEnum::tryFrom(0));
        $this->assertSame(FansStatusEnum::FOLLOWED, FansStatusEnum::tryFrom(1));
        $this->assertSame(FansStatusEnum::SILENT_AUTH, FansStatusEnum::tryFrom(2));
    }

    /**
     * 测试 isFans 方法
     */
    public function testIsFansIdentifiesFollowers(): void
    {
        $this->assertFalse(FansStatusEnum::UNFOLLOWED->isFans());
        $this->assertTrue(FansStatusEnum::FOLLOWED->isFans());
        $this->assertFalse(FansStatusEnum::SILENT_AUTH->isFans());
    }

    /**
     * 测试 toArray 方法
     */
    public function testToArray(): void
    {
        $expected = [
            'value' => 0,
            'label' => '已取关',
        ];
        $this->assertSame($expected, FansStatusEnum::UNFOLLOWED->toArray());
    }
}
