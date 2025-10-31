<?php

namespace YouzanApiUserBundle\Tests\Entity;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\RunTestsInSeparateProcesses;
use PHPUnit\Framework\TestCase;
use Tourze\PHPUnitDoctrineEntity\AbstractEntityTestCase;
use YouzanApiUserBundle\Entity\User;
use YouzanApiUserBundle\Entity\WechatInfo;
use YouzanApiUserBundle\Enum\FansStatusEnum;
use YouzanApiUserBundle\Enum\WechatTypeEnum;

/**
 * @internal
 */
#[CoversClass(WechatInfo::class)]
final class WechatInfoTest extends AbstractEntityTestCase
{
    protected function createEntity(): object
    {
        return new WechatInfo();
    }

    /**
     * @return iterable<string, array{string, mixed}>
     */
    public static function propertiesProvider(): iterable
    {
        $user = new User();
        $wechatType = WechatTypeEnum::OFFICIAL_ACCOUNT;
        $fansStatus = FansStatusEnum::FOLLOWED;
        $unionId = 'u_xyz987654321';
        $followTime = new \DateTimeImmutable('2025-01-01 10:00:00');
        $lastTalkTime = new \DateTimeImmutable('2025-01-02 14:30:00');
        $unfollowTime = new \DateTimeImmutable('2025-01-03 16:00:00');

        return [
            'user' => ['user', $user],
            'wechatType' => ['wechatType', $wechatType],
            'wechatType-null' => ['wechatType', null],
            'fansStatus' => ['fansStatus', $fansStatus],
            'unionId' => ['unionId', $unionId],
            'unionId-null' => ['unionId', null],
            'followTime' => ['followTime', $followTime],
            'followTime-null' => ['followTime', null],
            'lastTalkTime' => ['lastTalkTime', $lastTalkTime],
            'lastTalkTime-null' => ['lastTalkTime', null],
            'unfollowTime' => ['unfollowTime', $unfollowTime],
            'unfollowTime-null' => ['unfollowTime', null],
        ];
    }

    public function testIsFans(): void
    {
        $wechatInfo = new WechatInfo();

        // 默认状态是未关注
        $this->assertFalse($wechatInfo->isFans());

        // 设置为已关注
        $wechatInfo->setFansStatus(FansStatusEnum::FOLLOWED);
        $this->assertTrue($wechatInfo->isFans());

        // 设置为未关注
        $wechatInfo->setFansStatus(FansStatusEnum::UNFOLLOWED);
        $this->assertFalse($wechatInfo->isFans());
    }
}
