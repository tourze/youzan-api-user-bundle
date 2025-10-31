<?php

namespace YouzanApiUserBundle\Tests\Entity;

use PHPUnit\Framework\Attributes\CoversClass;
use Tourze\PHPUnitDoctrineEntity\AbstractEntityTestCase;
use YouzanApiBundle\Entity\Account;
use YouzanApiUserBundle\Entity\User;
use YouzanApiUserBundle\Enum\GenderEnum;

/**
 * @internal
 */
#[CoversClass(User::class)]
final class UserTest extends AbstractEntityTestCase
{
    protected function createEntity(): object
    {
        return new User();
    }

    /**
     * @return iterable<string, array{string, mixed}>
     */
    public static function propertiesProvider(): iterable
    {
        $account = new Account();

        return [
            'yzOpenId' => ['yzOpenId', 'test_value'],
            'gender' => ['gender', GenderEnum::MALE],
            'platformType' => ['platformType', 123],
            'account' => ['account', $account],
        ];
    }

    public function testToStringMethod(): void
    {
        $user = new User();

        // 新创建的实体 ID 为 0，应该返回空字符串
        $this->assertEquals('', (string) $user);

        // 即使设置了昵称，ID 仍为 0，应该返回空字符串
        $user->setNickNameDecrypted('TestUser');
        $user->setYzOpenId('yz_test123');
        $this->assertEquals('', (string) $user);
    }

    public function testGenderHandling(): void
    {
        $user = new User();

        // 默认应该是 UNKNOWN
        $this->assertEquals(GenderEnum::UNKNOWN, $user->getGender());

        // 测试设置性别
        $user->setGender(GenderEnum::MALE);
        $this->assertEquals(GenderEnum::MALE, $user->getGender());

        $user->setGender(GenderEnum::FEMALE);
        $this->assertEquals(GenderEnum::FEMALE, $user->getGender());
    }

    public function testAccountAssociation(): void
    {
        $user = new User();
        $account = new Account();

        // 测试设置账号
        $user->setAccount($account);
        $this->assertSame($account, $user->getAccount());
    }
}
