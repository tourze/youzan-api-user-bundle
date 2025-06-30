<?php

namespace YouzanApiUserBundle\Tests\Entity;

use PHPUnit\Framework\TestCase;
use YouzanApiUserBundle\Entity\WechatInfo;
use YouzanApiUserBundle\Entity\User;
use YouzanApiUserBundle\Enum\WechatTypeEnum;
use YouzanApiUserBundle\Enum\FansStatusEnum;

class WechatInfoTest extends TestCase
{
    private WechatInfo $wechatInfo;

    protected function setUp(): void
    {
        $this->wechatInfo = new WechatInfo();
    }

    public function testGettersAndSetters(): void
    {
        // Test User
        $user = new User();
        $this->wechatInfo->setUser($user);
        $this->assertSame($user, $this->wechatInfo->getUser());

        // Test wechatType
        $wechatType = WechatTypeEnum::OFFICIAL_ACCOUNT;
        $this->wechatInfo->setWechatType($wechatType);
        $this->assertEquals($wechatType, $this->wechatInfo->getWechatType());

        // Test null wechatType
        $this->wechatInfo->setWechatType(null);
        $this->assertNull($this->wechatInfo->getWechatType());

        // Test fansStatus
        $fansStatus = FansStatusEnum::FOLLOWED;
        $this->wechatInfo->setFansStatus($fansStatus);
        $this->assertEquals($fansStatus, $this->wechatInfo->getFansStatus());

        // Test unionId
        $unionId = 'u_xyz987654321';
        $this->wechatInfo->setUnionId($unionId);
        $this->assertEquals($unionId, $this->wechatInfo->getUnionId());

        // Test followTime
        $followTime = new \DateTimeImmutable('2025-01-01 10:00:00');
        $this->wechatInfo->setFollowTime($followTime);
        $this->assertEquals($followTime, $this->wechatInfo->getFollowTime());

        // Test lastTalkTime
        $lastTalkTime = new \DateTimeImmutable('2025-01-02 14:30:00');
        $this->wechatInfo->setLastTalkTime($lastTalkTime);
        $this->assertEquals($lastTalkTime, $this->wechatInfo->getLastTalkTime());

        // Test unfollowTime
        $unfollowTime = new \DateTimeImmutable('2025-01-03 16:00:00');
        $this->wechatInfo->setUnfollowTime($unfollowTime);
        $this->assertEquals($unfollowTime, $this->wechatInfo->getUnfollowTime());

        // Test null values
        $this->wechatInfo->setUnionId(null);
        $this->assertNull($this->wechatInfo->getUnionId());
        
        $this->wechatInfo->setFollowTime(null);
        $this->assertNull($this->wechatInfo->getFollowTime());
        
        $this->wechatInfo->setLastTalkTime(null);
        $this->assertNull($this->wechatInfo->getLastTalkTime());
        
        $this->wechatInfo->setUnfollowTime(null);
        $this->assertNull($this->wechatInfo->getUnfollowTime());
    }

    public function testIsFans(): void
    {
        $user = new User();
        $this->wechatInfo->setUser($user);
        
        // 默认状态是未关注
        $this->assertFalse($this->wechatInfo->isFans());
        
        // 设置为已关注
        $this->wechatInfo->setFansStatus(FansStatusEnum::FOLLOWED);
        $this->assertTrue($this->wechatInfo->isFans());
        
        // 设置为未关注
        $this->wechatInfo->setFansStatus(FansStatusEnum::UNFOLLOWED);
        $this->assertFalse($this->wechatInfo->isFans());
    }
}