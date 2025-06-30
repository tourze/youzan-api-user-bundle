<?php

namespace YouzanApiUserBundle\Tests\Entity;

use PHPUnit\Framework\TestCase;
use YouzanApiUserBundle\Entity\LevelInfo;
use YouzanApiUserBundle\Entity\User;

class LevelInfoTest extends TestCase
{
    private LevelInfo $levelInfo;

    protected function setUp(): void
    {
        $this->levelInfo = new LevelInfo();
    }

    public function testGettersAndSetters(): void
    {
        // Test levelId
        $levelId = 12345;
        $this->levelInfo->setLevelId($levelId);
        $this->assertEquals($levelId, $this->levelInfo->getLevelId());

        // Test null levelId
        $this->levelInfo->setLevelId(null);
        $this->assertNull($this->levelInfo->getLevelId());

        // Test levelName
        $levelName = 'VIP会员';
        $this->levelInfo->setLevelName($levelName);
        $this->assertEquals($levelName, $this->levelInfo->getLevelName());

        // Test null levelName
        $this->levelInfo->setLevelName(null);
        $this->assertNull($this->levelInfo->getLevelName());
    }

    public function testToString(): void
    {
        // 当 ID 为 0 时（默认值），应该返回 '[]'
        $this->assertEquals('[]', (string)$this->levelInfo);

        $this->levelInfo->setLevelId(123);
        $this->levelInfo->setLevelName('黄金会员');
        $this->assertEquals('黄金会员[123]', (string)$this->levelInfo);
    }
}