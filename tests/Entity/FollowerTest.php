<?php

namespace YouzanApiUserBundle\Tests\Entity;

use PHPUnit\Framework\Attributes\CoversClass;
use Tourze\PHPUnitDoctrineEntity\AbstractEntityTestCase;
use YouzanApiBundle\Entity\Account;
use YouzanApiUserBundle\Entity\Follower;
use YouzanApiUserBundle\Entity\LevelInfo;
use YouzanApiUserBundle\Enum\GenderEnum;

/**
 * @internal
 */
#[CoversClass(Follower::class)]
final class FollowerTest extends AbstractEntityTestCase
{
    protected function createEntity(): object
    {
        return new Follower();
    }

    /**
     * @return iterable<string, array{string, mixed}>
     */
    public static function propertiesProvider(): iterable
    {
        $account = new Account();

        return [
            'userId' => ['userId', 123],
            'weixinOpenId' => ['weixinOpenId', 'test_value'],
            'sex' => ['sex', GenderEnum::MALE],
            'account' => ['account', $account],
        ];
    }

    public function testIsFollowMethod(): void
    {
        $follower = new Follower();

        // 默认应该是 false
        $this->assertFalse($follower->isFollow());

        // 设置为 true
        $follower->setIsFollow(true);
        $this->assertTrue($follower->isFollow());

        // 切换回 false
        $follower->setIsFollow(false);
        $this->assertFalse($follower->isFollow());
    }

    public function testToStringMethod(): void
    {
        $follower = new Follower();

        // 新创建的实体 ID 为 0，应该返回空字符串
        $this->assertEquals('', (string) $follower);

        // 即使设置了昵称，ID 仍为 0，应该返回空字符串
        $follower->setNick('TestUser');
        $follower->setWeixinOpenId('o_test123');
        $this->assertEquals('', (string) $follower);
    }

    public function testLevelInfoAssociation(): void
    {
        $follower = new Follower();
        $levelInfo = new LevelInfo();

        // 测试设置等级信息
        $follower->setLevelInfo($levelInfo);
        $this->assertSame($levelInfo, $follower->getLevelInfo());

        // 测试设置为 null
        $follower->setLevelInfo(null);
        $this->assertNull($follower->getLevelInfo());
    }
}
