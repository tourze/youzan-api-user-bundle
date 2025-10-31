<?php

namespace YouzanApiUserBundle\Tests\Repository;

use Doctrine\ORM\Persisters\Exception\UnrecognizedField;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\RunTestsInSeparateProcesses;
use PHPUnit\Framework\Attributes\TestWith;
use Tourze\PHPUnitSymfonyKernelTest\AbstractRepositoryTestCase;
use YouzanApiBundle\Entity\Account;
use YouzanApiUserBundle\Entity\MobileInfo;
use YouzanApiUserBundle\Entity\User;
use YouzanApiUserBundle\Entity\WechatInfo;
use YouzanApiUserBundle\Enum\FansStatusEnum;
use YouzanApiUserBundle\Enum\GenderEnum;
use YouzanApiUserBundle\Repository\MobileInfoRepository;
use YouzanApiUserBundle\Repository\UserRepository;
use YouzanApiUserBundle\Repository\WechatInfoRepository;

/**
 * @internal
 */
#[CoversClass(UserRepository::class)]
#[RunTestsInSeparateProcesses]
final class UserRepositoryTest extends AbstractRepositoryTestCase
{
    private UserRepository $repository;

    protected function onSetUp(): void
    {
        $this->repository = self::getService(UserRepository::class);
    }

    public function testCanBeInstantiated(): void
    {
        $this->assertInstanceOf(UserRepository::class, $this->repository);
        $this->assertSame(User::class, $this->repository->getClassName());
    }

    public function testGetClassName(): void
    {
        $this->assertSame(User::class, $this->repository->getClassName());
    }

    public function testFindOneByNonExistentField(): void
    {
        $this->expectException(UnrecognizedField::class);
        $this->repository->findOneBy(['nonExistentField' => 'value']);
    }

    public function testFindByYzOpenId(): void
    {
        $user = $this->createTestUser('test_yz_open_id');
        self::getEntityManager()->persist($user);
        self::getEntityManager()->flush();

        $foundUser = $this->repository->findByYzOpenId('test_yz_open_id');
        $this->assertInstanceOf(User::class, $foundUser);
        $this->assertSame('test_yz_open_id', $foundUser->getYzOpenId());
    }

    public function testFindByYzOpenIdReturnsNullForNonExistentId(): void
    {
        $result = $this->repository->findByYzOpenId('non_existent_yz_open_id');
        $this->assertNull($result);
    }

    public function testFindByUnionId(): void
    {
        $user = $this->createTestUserWithWechatInfo('yz_open_id_union', 'test_union_id');
        self::getEntityManager()->persist($user);
        self::getEntityManager()->flush();

        $foundUser = $this->repository->findByUnionId('test_union_id');
        $this->assertInstanceOf(User::class, $foundUser);
        $this->assertSame('yz_open_id_union', $foundUser->getYzOpenId());

        // 通过 WechatInfo repository 查询微信信息
        $wechatInfoRepo = self::getService(WechatInfoRepository::class);
        $wechatInfo = $wechatInfoRepo->findOneBy(['user' => $foundUser]);
        $this->assertNotNull($wechatInfo);
        $this->assertSame('test_union_id', $wechatInfo->getUnionId());
    }

    public function testFindByUnionIdReturnsNullForNonExistentId(): void
    {
        $result = $this->repository->findByUnionId('non_existent_union_id');
        $this->assertNull($result);
    }

    public function testFindByWeixinOpenId(): void
    {
        $user = $this->createTestUserWithWechatInfo('yz_open_id_weixin', 'test_weixin_open_id');
        self::getEntityManager()->persist($user);
        self::getEntityManager()->flush();

        $foundUser = $this->repository->findByWeixinOpenId('test_weixin_open_id');
        $this->assertInstanceOf(User::class, $foundUser);
        $this->assertSame('yz_open_id_weixin', $foundUser->getYzOpenId());

        // 通过 WechatInfo repository 查询微信信息
        $wechatInfoRepo = self::getService(WechatInfoRepository::class);
        $wechatInfo = $wechatInfoRepo->findOneBy(['user' => $foundUser]);
        $this->assertNotNull($wechatInfo);
        $this->assertSame('test_weixin_open_id', $wechatInfo->getUnionId());
    }

    public function testFindByWeixinOpenIdReturnsNullForNonExistentId(): void
    {
        $result = $this->repository->findByWeixinOpenId('non_existent_weixin_open_id');
        $this->assertNull($result);
    }

    public function testFindByMobile(): void
    {
        $user = $this->createTestUserWithMobileInfo('yz_open_id_mobile', '13800138000', '+86');
        self::getEntityManager()->persist($user);
        self::getEntityManager()->flush();

        $foundUser = $this->repository->findByMobile('13800138000');
        $this->assertInstanceOf(User::class, $foundUser);
        $this->assertSame('yz_open_id_mobile', $foundUser->getYzOpenId());

        // 通过 MobileInfo repository 查询手机信息
        $mobileInfoRepo = self::getService(MobileInfoRepository::class);
        $mobileInfo = $mobileInfoRepo->findOneBy(['user' => $foundUser]);
        $this->assertNotNull($mobileInfo);
        $this->assertSame('13800138000', $mobileInfo->getMobileDecrypted());
    }

    public function testFindByMobileWithCountryCode(): void
    {
        $user = $this->createTestUserWithMobileInfo('yz_open_id_mobile_cc', '13800138001', '+1');
        self::getEntityManager()->persist($user);
        self::getEntityManager()->flush();

        $foundUser = $this->repository->findByMobile('13800138001', '+1');
        $this->assertInstanceOf(User::class, $foundUser);
        $this->assertSame('yz_open_id_mobile_cc', $foundUser->getYzOpenId());

        // 通过 MobileInfo repository 查询手机信息
        $mobileInfoRepo = self::getService(MobileInfoRepository::class);
        $mobileInfo = $mobileInfoRepo->findOneBy(['user' => $foundUser]);
        $this->assertNotNull($mobileInfo);
        $this->assertSame('+1', $mobileInfo->getCountryCode());
    }

    public function testFindByMobileReturnsNullForNonExistentMobile(): void
    {
        $result = $this->repository->findByMobile('99999999999');
        $this->assertNull($result);
    }

    public function testFindByFollowTimeRange(): void
    {
        $followTime = new \DateTimeImmutable('2023-06-15 10:00:00');
        $user = $this->createTestUserWithWechatInfo('yz_open_id_follow_time', 'union_id_follow');

        // 持久化用户和微信信息
        self::getEntityManager()->persist($user);
        self::getEntityManager()->flush();

        // 通过 WechatInfo repository 查询并设置关注时间
        $wechatInfoRepo = self::getService(WechatInfoRepository::class);
        $wechatInfo = $wechatInfoRepo->findOneBy(['user' => $user]);
        if (null !== $wechatInfo) {
            $wechatInfo->setFollowTime($followTime);
        }
        self::getEntityManager()->flush();

        $startTime = new \DateTime('2023-01-01');
        $endTime = new \DateTime('2023-12-31');
        $users = $this->repository->findByFollowTimeRange($startTime, $endTime);

        $this->assertIsArray($users);
        $this->assertGreaterThanOrEqual(1, count($users));

        $foundUser = null;
        foreach ($users as $u) {
            if ('yz_open_id_follow_time' === $u->getYzOpenId()) {
                $foundUser = $u;
                break;
            }
        }

        $this->assertNotNull($foundUser);
        $this->assertSame('yz_open_id_follow_time', $foundUser->getYzOpenId());
    }

    public function testFindByFollowTimeRangeReturnsEmptyForNoMatches(): void
    {
        $startTime = new \DateTime('1999-01-01');
        $endTime = new \DateTime('1999-12-31');
        $users = $this->repository->findByFollowTimeRange($startTime, $endTime);

        $this->assertIsArray($users);
        $this->assertEmpty($users);
    }

    public function testFindAllFans(): void
    {
        $user = $this->createTestUserWithWechatInfo('yz_open_id_fan', 'union_id_fan');
        self::getEntityManager()->persist($user);
        self::getEntityManager()->flush();

        // 通过 WechatInfo repository 设置粉丝状态
        $wechatInfoRepo = self::getService(WechatInfoRepository::class);
        $wechatInfo = $wechatInfoRepo->findOneBy(['user' => $user]);
        if (null !== $wechatInfo) {
            $wechatInfo->setFansStatus(FansStatusEnum::FOLLOWED);
        }
        self::getEntityManager()->flush();

        $fans = $this->repository->findAllFans();
        $this->assertIsArray($fans);
        $this->assertGreaterThanOrEqual(1, count($fans));

        $foundFan = null;
        foreach ($fans as $fan) {
            if ('yz_open_id_fan' === $fan->getYzOpenId()) {
                $foundFan = $fan;
                break;
            }
        }

        $this->assertNotNull($foundFan);

        // 通过 WechatInfo repository 验证粉丝状态
        $wechatInfo = $wechatInfoRepo->findOneBy(['user' => $foundFan]);
        $this->assertNotNull($wechatInfo);
        $this->assertSame(FansStatusEnum::FOLLOWED, $wechatInfo->getFansStatus());
    }

    public function testFindByPlatformType(): void
    {
        $user = $this->createTestUser('yz_open_id_platform_type', 5);
        self::getEntityManager()->persist($user);
        self::getEntityManager()->flush();

        $users = $this->repository->findByPlatformType(5);
        $this->assertIsArray($users);
        $this->assertGreaterThanOrEqual(1, count($users));

        $foundUser = null;
        foreach ($users as $u) {
            if ('yz_open_id_platform_type' === $u->getYzOpenId()) {
                $foundUser = $u;
                break;
            }
        }

        $this->assertNotNull($foundUser);
        $this->assertSame(5, $foundUser->getPlatformType());
    }

    public function testSave(): void
    {
        $user = $this->createTestUser('yz_open_id_save');

        $this->repository->save($user);

        $savedUser = $this->repository->findByYzOpenId('yz_open_id_save');
        $this->assertInstanceOf(User::class, $savedUser);
        $this->assertSame('yz_open_id_save', $savedUser->getYzOpenId());
    }

    public function testSaveWithoutFlush(): void
    {
        $user = $this->createTestUser('yz_open_id_save_no_flush');

        $this->repository->save($user, false);

        self::getEntityManager()->flush();

        $savedUser = $this->repository->findByYzOpenId('yz_open_id_save_no_flush');
        $this->assertInstanceOf(User::class, $savedUser);
        $this->assertSame('yz_open_id_save_no_flush', $savedUser->getYzOpenId());
    }

    public function testRemove(): void
    {
        $user = $this->createTestUser('yz_open_id_remove');
        self::getEntityManager()->persist($user);
        self::getEntityManager()->flush();

        $userId = $user->getId();

        $this->repository->remove($user);

        $removedUser = $this->repository->find($userId);
        $this->assertNull($removedUser);
    }

    private function createTestUser(string $yzOpenId = 'test_yz_open_id', int $platformType = 1): User
    {
        $account = $this->createTestAccount();
        self::getEntityManager()->persist($account);

        $user = new User();
        $user->setYzOpenId($yzOpenId);
        $user->setNickNameDecrypted('Test User');
        $user->setGender(GenderEnum::UNKNOWN);
        $user->setPlatformType($platformType);
        $user->setAccount($account);

        return $user;
    }

    private function createTestUserWithWechatInfo(string $yzOpenId, string $unionId): User
    {
        $user = $this->createTestUser($yzOpenId);

        $wechatInfo = new WechatInfo();
        $wechatInfo->setUnionId($unionId);
        $wechatInfo->setFansStatus(FansStatusEnum::FOLLOWED);
        $wechatInfo->setUser($user);

        self::getEntityManager()->persist($wechatInfo);

        return $user;
    }

    private function createTestUserWithMobileInfo(string $yzOpenId, string $mobile, string $countryCode = '+86'): User
    {
        $user = $this->createTestUser($yzOpenId);

        $mobileInfo = new MobileInfo();
        $mobileInfo->setMobileDecrypted($mobile);
        $mobileInfo->setCountryCode($countryCode);
        $mobileInfo->setUser($user);

        self::getEntityManager()->persist($mobileInfo);

        return $user;
    }

    private function createTestAccount(): Account
    {
        $account = new Account();
        $account->setName('Test Account');
        $account->setClientId('test_client_id_' . uniqid());
        $account->setClientSecret('test_client_secret');

        return $account;
    }

    public function testFindOneByWithOrderBy(): void
    {
        $user1 = $this->createTestUser('yz_open_id_order_1', 1);
        $user2 = $this->createTestUser('yz_open_id_order_2', 1);

        self::getEntityManager()->persist($user1);
        self::getEntityManager()->persist($user2);
        self::getEntityManager()->flush();

        $foundUser = $this->repository->findOneBy(
            ['platformType' => 1],
            ['id' => 'DESC']
        );

        $this->assertInstanceOf(User::class, $foundUser);
        $this->assertSame(1, $foundUser->getPlatformType());
    }

    public function testFindByNullableFields(): void
    {
        $user = $this->createTestUser('yz_open_id_nullable');
        self::getEntityManager()->persist($user);
        self::getEntityManager()->flush();

        $users = $this->repository->findBy(['nickNameEncrypted' => null]);
        $this->assertIsArray($users);
        $this->assertGreaterThanOrEqual(1, count($users));
    }

    public function testCountNullableFields(): void
    {
        $user = $this->createTestUser('yz_open_id_count_null');
        self::getEntityManager()->persist($user);
        self::getEntityManager()->flush();

        $count = $this->repository->count(['nickNameEncrypted' => null]);
        $this->assertGreaterThanOrEqual(1, $count);
    }

    public function testFindByAssociation(): void
    {
        $user = $this->createTestUserWithWechatInfo('yz_open_id_assoc', 'union_id_assoc');
        self::getEntityManager()->persist($user);
        self::getEntityManager()->flush();

        $users = $this->repository->findBy(['account' => $user->getAccount()]);
        $this->assertIsArray($users);
        $this->assertGreaterThanOrEqual(1, count($users));
    }

    public function testCountByAssociation(): void
    {
        $user = $this->createTestUserWithWechatInfo('yz_open_id_count_assoc', 'union_id_count_assoc');
        self::getEntityManager()->persist($user);
        self::getEntityManager()->flush();

        $count = $this->repository->count(['account' => $user->getAccount()]);
        $this->assertGreaterThanOrEqual(1, $count);
    }

    public function testFindOneByWithSortingLogic(): void
    {
        $user1 = $this->createTestUser('yz_open_id_sort_1', 1);
        $user2 = $this->createTestUser('yz_open_id_sort_2', 1);
        $user3 = $this->createTestUser('yz_open_id_sort_3', 1);

        self::getEntityManager()->persist($user1);
        self::getEntityManager()->persist($user2);
        self::getEntityManager()->persist($user3);
        self::getEntityManager()->flush();

        $foundUserAsc = $this->repository->findOneBy(
            ['platformType' => 1],
            ['id' => 'ASC']
        );
        $foundUserDesc = $this->repository->findOneBy(
            ['platformType' => 1],
            ['id' => 'DESC']
        );

        $this->assertInstanceOf(User::class, $foundUserAsc);
        $this->assertInstanceOf(User::class, $foundUserDesc);
        $this->assertNotSame($foundUserAsc->getId(), $foundUserDesc->getId());
    }

    public function testCountByAssociationField(): void
    {
        $user = $this->createTestUserWithWechatInfo('yz_open_id_count_assoc_field', 'union_id_count_assoc_field');
        self::getEntityManager()->persist($user);
        self::getEntityManager()->flush();

        $count = $this->repository->count(['account' => $user->getAccount()]);
        $this->assertGreaterThanOrEqual(1, $count);
    }

    public function testFindByAssociationField(): void
    {
        $user = $this->createTestUserWithWechatInfo('yz_open_id_find_assoc_field', 'union_id_find_assoc_field');
        self::getEntityManager()->persist($user);
        self::getEntityManager()->flush();

        $users = $this->repository->findBy(['account' => $user->getAccount()]);
        $this->assertIsArray($users);
        $this->assertGreaterThanOrEqual(1, count($users));
        foreach ($users as $foundUser) {
            $this->assertSame($user->getAccount()->getId(), $foundUser->getAccount()->getId());
        }
    }

    public function testFindByNullableFieldIsNull(): void
    {
        $user = $this->createTestUser('yz_open_id_null_check');
        self::getEntityManager()->persist($user);
        self::getEntityManager()->flush();

        $users = $this->repository->findBy(['nickNameEncrypted' => null]);
        $this->assertIsArray($users);
        $this->assertGreaterThanOrEqual(1, count($users));
        foreach ($users as $foundUser) {
            $this->assertNull($foundUser->getNickNameEncrypted());
        }
    }

    public function testCountByNullableFieldIsNull(): void
    {
        $user = $this->createTestUser('yz_open_id_count_null_check');
        self::getEntityManager()->persist($user);
        self::getEntityManager()->flush();

        $count = $this->repository->count(['nickNameEncrypted' => null]);
        $this->assertGreaterThanOrEqual(1, $count);
    }

    protected function createNewEntity(): object
    {
        $account = $this->createTestAccount();
        $user = new User();
        $user->setYzOpenId('test_yz_open_id_' . uniqid());
        $user->setAccount($account);

        return $user;
    }

    protected function getRepository(): UserRepository
    {
        return $this->repository;
    }

    /**
     * 测试不存在的关联字段排序会失败（预期行为）
     * 这些字段已从 User 实体中移除，现在是单向关联
     */
    #[TestWith(['staff'])]
    #[TestWith(['wechatInfo'])]
    #[TestWith(['mobileInfo'])]
    public function testFindOneByShouldFailForInverseAssociations(string $field): void
    {
        $this->expectException(UnrecognizedField::class);
        $this->expectExceptionMessage('Unrecognized field');

        $this->repository->findOneBy(['id' => -999], [$field => 'ASC']);
    }
}
