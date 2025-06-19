<?php

namespace YouzanApiUserBundle\Tests\Entity;

use PHPUnit\Framework\TestCase;
use YouzanApiBundle\Entity\Account;
use YouzanApiUserBundle\Entity\Follower;
use YouzanApiUserBundle\Entity\LevelInfo;
use YouzanApiUserBundle\Enum\GenderEnum;

class FollowerTest extends TestCase
{
    private Follower $follower;

    protected function setUp(): void
    {
        $this->follower = new Follower();
    }

    /**
     * 测试 ID getter
     */
    public function testGetIdReturnsNullForNewEntity(): void
    {
        $this->assertSame(0, $this->follower->getId());
    }

    /**
     * 测试 UserId getter 和 setter
     */
    public function testUserIdGetterAndSetter(): void
    {
        $value = 123456789;

        $returnValue = $this->follower->setUserId($value);

        $this->assertSame($this->follower, $returnValue, 'Setter 应该返回 $this 以支持链式调用');
        $this->assertSame($value, $this->follower->getUserId(), 'Getter 应该返回设置的值');
    }

    /**
     * 测试 WeixinOpenId getter 和 setter
     */
    public function testWeixinOpenIdGetterAndSetter(): void
    {
        $value = 'o_abc123456789';

        $returnValue = $this->follower->setWeixinOpenId($value);

        $this->assertSame($this->follower, $returnValue);
        $this->assertSame($value, $this->follower->getWeixinOpenId());
    }

    /**
     * 测试昵称相关的 getter 和 setter
     */
    public function testNickGetterAndSetter(): void
    {
        $value = 'Test Nickname';

        $returnValue = $this->follower->setNick($value);

        $this->assertSame($this->follower, $returnValue);
        $this->assertSame($value, $this->follower->getNick());

        // 测试 null 值
        $this->follower->setNick(null);
        $this->assertNull($this->follower->getNick());
    }

    /**
     * 测试头像相关的 getter 和 setter
     */
    public function testAvatarGetterAndSetter(): void
    {
        $value = 'https://example.com/avatar.jpg';

        $returnValue = $this->follower->setAvatar($value);

        $this->assertSame($this->follower, $returnValue);
        $this->assertSame($value, $this->follower->getAvatar());

        // 测试 null 值
        $this->follower->setAvatar(null);
        $this->assertNull($this->follower->getAvatar());
    }

    /**
     * 测试地理位置相关的 getter 和 setter
     */
    public function testLocationGettersAndSetters(): void
    {
        $country = 'China';
        $province = 'Guangdong';
        $city = 'Shenzhen';

        $this->follower->setCountry($country)
            ->setProvince($province)
            ->setCity($city);

        $this->assertSame($country, $this->follower->getCountry());
        $this->assertSame($province, $this->follower->getProvince());
        $this->assertSame($city, $this->follower->getCity());

        // 测试 null 值
        $this->follower->setCountry(null)
            ->setProvince(null)
            ->setCity(null);

        $this->assertNull($this->follower->getCountry());
        $this->assertNull($this->follower->getProvince());
        $this->assertNull($this->follower->getCity());
    }

    /**
     * 测试性别相关的 getter 和 setter
     */
    public function testSexGetterAndSetter(): void
    {
        $value = GenderEnum::MALE;

        $returnValue = $this->follower->setSex($value);

        $this->assertSame($this->follower, $returnValue);
        $this->assertSame($value, $this->follower->getSex());

        // 测试其他枚举值
        $this->follower->setSex(GenderEnum::FEMALE);
        $this->assertSame(GenderEnum::FEMALE, $this->follower->getSex());
    }

    /**
     * 测试关注状态相关的 getter 和 setter
     */
    public function testIsFollowGetterAndSetter(): void
    {
        // 默认应该是 false
        $this->assertFalse($this->follower->isFollow());

        $value = true;
        $returnValue = $this->follower->setIsFollow($value);

        $this->assertSame($this->follower, $returnValue);
        $this->assertTrue($this->follower->isFollow());

        // 切换回 false
        $this->follower->setIsFollow(false);
        $this->assertFalse($this->follower->isFollow());
    }

    /**
     * 测试关注时间相关的 getter 和 setter
     */
    public function testFollowTimeGetterAndSetter(): void
    {
        $value = 1609459200; // 2021-01-01 00:00:00

        $returnValue = $this->follower->setFollowTime($value);

        $this->assertSame($this->follower, $returnValue);
        $this->assertSame($value, $this->follower->getFollowTime());

        // 测试 null 值
        $this->follower->setFollowTime(null);
        $this->assertNull($this->follower->getFollowTime());
    }

    /**
     * 测试交易笔数相关的 getter 和 setter
     */
    public function testTradedNumGetterAndSetter(): void
    {
        $value = 10;

        $returnValue = $this->follower->setTradedNum($value);

        $this->assertSame($this->follower, $returnValue);
        $this->assertSame($value, $this->follower->getTradedNum());

        // 测试 null 值
        $this->follower->setTradedNum(null);
        $this->assertNull($this->follower->getTradedNum());
    }

    /**
     * 测试交易金额相关的 getter 和 setter
     */
    public function testTradeMoneyGetterAndSetter(): void
    {
        $value = 1000.50;

        $returnValue = $this->follower->setTradeMoney($value);

        $this->assertSame($this->follower, $returnValue);
        $this->assertSame($value, $this->follower->getTradeMoney());

        // 测试 null 值
        $this->follower->setTradeMoney(null);
        $this->assertNull($this->follower->getTradeMoney());
    }

    /**
     * 测试积分信息相关的 getter 和 setter
     */
    public function testPointsGetterAndSetter(): void
    {
        $value = ['total' => 100, 'available' => 50];

        $returnValue = $this->follower->setPoints($value);

        $this->assertSame($this->follower, $returnValue);
        $this->assertSame($value, $this->follower->getPoints());

        // 测试 null 值
        $this->follower->setPoints(null);
        $this->assertNull($this->follower->getPoints());
    }

    /**
     * 测试关联的账号
     */
    public function testAccountAssociation(): void
    {
        $account = $this->createMock(Account::class);

        $returnValue = $this->follower->setAccount($account);

        $this->assertSame($this->follower, $returnValue);
        $this->assertSame($account, $this->follower->getAccount());
    }

    /**
     * 测试关联的等级信息
     */
    public function testLevelInfoAssociation(): void
    {
        $levelInfo = $this->createMock(LevelInfo::class);

        $returnValue = $this->follower->setLevelInfo($levelInfo);

        $this->assertSame($this->follower, $returnValue);
        $this->assertSame($levelInfo, $this->follower->getLevelInfo());

        // 测试 null 值
        $this->follower->setLevelInfo(null);
        $this->assertNull($this->follower->getLevelInfo());
    }

    /**
     * 测试时间戳相关方法
     */
    public function testTimestampMethods(): void
    {
        $now = new \DateTimeImmutable();

        // 测试创建时间
        $this->follower->setCreateTime($now);
        $this->assertSame($now, $this->follower->getCreateTime());

        // 测试更新时间
        $this->follower->setUpdateTime($now);
        $this->assertSame($now, $this->follower->getUpdateTime());

        // 测试 null 值
        $this->follower->setCreateTime(null);
        $this->follower->setUpdateTime(null);

        $this->assertNull($this->follower->getCreateTime());
        $this->assertNull($this->follower->getUpdateTime());
    }
}
