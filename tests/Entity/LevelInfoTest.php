<?php

namespace YouzanApiUserBundle\Tests\Entity;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\RunTestsInSeparateProcesses;
use PHPUnit\Framework\TestCase;
use Tourze\PHPUnitDoctrineEntity\AbstractEntityTestCase;
use YouzanApiUserBundle\Entity\LevelInfo;

/**
 * @internal
 */
#[CoversClass(LevelInfo::class)]
final class LevelInfoTest extends AbstractEntityTestCase
{
    protected function createEntity(): object
    {
        return new LevelInfo();
    }

    /**
     * @return iterable<string, array{string, mixed}>
     */
    public static function propertiesProvider(): iterable
    {
        return [
            'levelId' => ['levelId', 12345],
            'levelId-null' => ['levelId', null],
            'levelName' => ['levelName', 'VIP会员'],
            'levelName-null' => ['levelName', null],
        ];
    }

    public function testToString(): void
    {
        $levelInfo = new LevelInfo();

        // 当 ID 为 0 时（默认值），应该返回 '[]'
        $this->assertEquals('[]', (string) $levelInfo);

        $levelInfo->setLevelId(123);
        $levelInfo->setLevelName('黄金会员');
        $this->assertEquals('黄金会员[123]', (string) $levelInfo);
    }
}
