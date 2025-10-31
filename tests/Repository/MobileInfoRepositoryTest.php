<?php

namespace YouzanApiUserBundle\Tests\Repository;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\RunTestsInSeparateProcesses;
use Tourze\PHPUnitSymfonyKernelTest\AbstractRepositoryTestCase;
use YouzanApiBundle\Entity\Account;
use YouzanApiUserBundle\Entity\MobileInfo;
use YouzanApiUserBundle\Entity\User;
use YouzanApiUserBundle\Enum\GenderEnum;
use YouzanApiUserBundle\Repository\MobileInfoRepository;

/**
 * @internal
 */
#[CoversClass(MobileInfoRepository::class)]
#[RunTestsInSeparateProcesses]
final class MobileInfoRepositoryTest extends AbstractRepositoryTestCase
{
    protected function onSetUp(): void
    {
    }

    protected function createNewEntity(): object
    {
        $account = $this->createTestAccount();
        $user = new User();
        $user->setYzOpenId('test_yz_open_id_' . uniqid());
        $user->setAccount($account);

        $mobileInfo = new MobileInfo();
        $mobileInfo->setUser($user);
        $mobileInfo->setCountryCode('86');
        $mobileInfo->setMobileDecrypted('13800138000');
        $mobileInfo->setMobileEncrypted('encrypted_mobile_' . uniqid());

        return $mobileInfo;
    }

    protected function getRepository(): MobileInfoRepository
    {
        return self::getService(MobileInfoRepository::class);
    }

    private function createTestAccount(string $name = 'Test Account', string $clientId = 'test_client_id', string $clientSecret = 'test_client_secret'): Account
    {
        $account = new Account();
        $account->setName($name);
        $account->setClientId($clientId . '_' . uniqid());
        $account->setClientSecret($clientSecret);

        $entityManager = self::getEntityManager();
        $entityManager->persist($account);
        $entityManager->flush();

        return $account;
    }

    private function createTestUser(Account $account, string $yzOpenId = 'test_yz_open_id'): User
    {
        $user = new User();
        $user->setYzOpenId($yzOpenId);
        $user->setAccount($account);
        $user->setGender(GenderEnum::UNKNOWN);
        $user->setPlatformType(1);

        $entityManager = self::getEntityManager();
        $entityManager->persist($user);
        $entityManager->flush();

        return $user;
    }

    private function createTestMobileInfo(
        User $user,
        ?string $countryCode = '86',
        ?string $mobileDecrypted = '13800138000',
        ?string $mobileEncrypted = 'encrypted_mobile_hash',
    ): MobileInfo {
        $mobileInfo = new MobileInfo();
        $mobileInfo->setUser($user);
        $mobileInfo->setCountryCode($countryCode);
        $mobileInfo->setMobileDecrypted($mobileDecrypted);
        $mobileInfo->setMobileEncrypted($mobileEncrypted);

        $entityManager = self::getEntityManager();
        $entityManager->persist($mobileInfo);
        $entityManager->flush();

        return $mobileInfo;
    }

    // ======== 基础功能测试 ========

    public function testFindByMobileDecryptedWithExistingMobile(): void
    {
        $repository = $this->getRepository();

        // 创建测试数据
        $account = $this->createTestAccount();
        $user = $this->createTestUser($account, 'test_user_1');
        $mobileInfo = $this->createTestMobileInfo($user, '86', '13800138001', 'encrypted_1');

        // 执行测试
        $result = $repository->findByMobileDecrypted('13800138001');

        // 验证结果
        $this->assertInstanceOf(MobileInfo::class, $result);
        $this->assertEquals('13800138001', $result->getMobileDecrypted());
        $this->assertEquals($mobileInfo->getId(), $result->getId());
    }

    public function testFindByMobileDecryptedWithNonExistingMobile(): void
    {
        $repository = $this->getRepository();

        // 执行测试
        $result = $repository->findByMobileDecrypted('13800138999');

        // 验证结果
        $this->assertNull($result);
    }

    public function testFindByMobileEncryptedWithExistingMobile(): void
    {
        $repository = $this->getRepository();

        // 创建测试数据
        $account = $this->createTestAccount();
        $user = $this->createTestUser($account, 'test_user_2');
        $mobileInfo = $this->createTestMobileInfo($user, '86', '13800138002', 'encrypted_hash_2');

        // 执行测试
        $result = $repository->findByMobileEncrypted('encrypted_hash_2');

        // 验证结果
        $this->assertInstanceOf(MobileInfo::class, $result);
        $this->assertEquals('encrypted_hash_2', $result->getMobileEncrypted());
        $this->assertEquals($mobileInfo->getId(), $result->getId());
    }

    public function testFindByMobileEncryptedWithNonExistingMobile(): void
    {
        $repository = $this->getRepository();

        // 执行测试
        $result = $repository->findByMobileEncrypted('non_existing_hash');

        // 验证结果
        $this->assertNull($result);
    }

    // ======== findBy 方法测试 ========

    // ======== findOneBy 方法测试 ========

    // ======== findAll 方法测试 ========

    // ======== find 方法测试 ========

    // ======== save 方法测试 ========

    public function testSave(): void
    {
        $repository = $this->getRepository();

        // 创建依赖数据
        $account = $this->createTestAccount();
        $user = $this->createTestUser($account, 'test_user_save');

        // 创建新实体
        $mobileInfo = new MobileInfo();
        $mobileInfo->setUser($user);
        $mobileInfo->setCountryCode('86');
        $mobileInfo->setMobileDecrypted('13800138120');
        $mobileInfo->setMobileEncrypted('encrypted_120');

        // 执行测试 - 保存但不刷新
        $repository->save($mobileInfo, false);

        // 验证实体在 EntityManager 中但未刷新到数据库
        $entityManager = self::getEntityManager();
        $this->assertTrue($entityManager->contains($mobileInfo));

        // 手动刷新
        $entityManager->flush();

        // 验证实体已保存到数据库
        $this->assertEntityPersisted($mobileInfo);

        // 测试带刷新的保存
        $account2 = $this->createTestAccount('Test Account 2', 'test_client_id_2', 'test_client_secret_2');
        $user2 = $this->createTestUser($account2, 'test_user_save_2');
        $mobileInfo2 = new MobileInfo();
        $mobileInfo2->setUser($user2);
        $mobileInfo2->setCountryCode('1');
        $mobileInfo2->setMobileDecrypted('15555551236');
        $mobileInfo2->setMobileEncrypted('encrypted_121');

        $repository->save($mobileInfo2, true);

        // 验证实体直接保存到数据库
        $this->assertEntityPersisted($mobileInfo2);
    }

    // ======== remove 方法测试 ========

    public function testRemove(): void
    {
        $repository = $this->getRepository();

        // 创建并保存测试数据
        $account = $this->createTestAccount();
        $user = $this->createTestUser($account, 'test_user_remove');
        $mobileInfo = $this->createTestMobileInfo($user, '86', '13800138130', 'encrypted_130');
        $mobileInfoId = $mobileInfo->getId();

        // 验证实体存在
        $this->assertEntityPersisted($mobileInfo);

        // 重新获取实体确保它在 EntityManager 上下文中
        $managedMobileInfo = self::getEntityManager()->find(MobileInfo::class, $mobileInfoId);
        $this->assertNotNull($managedMobileInfo);

        // 执行测试 - 删除但不刷新
        $repository->remove($managedMobileInfo, false);

        // 验证实体仍在数据库中（因为未刷新）
        $found = self::getEntityManager()->find(MobileInfo::class, $mobileInfoId);
        $this->assertNotNull($found);

        // 手动刷新
        self::getEntityManager()->flush();

        // 清理缓存并验证实体已删除
        self::getEntityManager()->clear();
        $this->assertEntityNotExists(MobileInfo::class, $mobileInfoId);

        // 测试带刷新的删除
        $account2 = $this->createTestAccount('Test Account Remove 2', 'test_client_remove_2', 'test_secret_remove_2');
        $user2 = $this->createTestUser($account2, 'test_user_remove_2');
        $mobileInfo2 = $this->createTestMobileInfo($user2, '86', '13800138131', 'encrypted_131');
        $mobileInfo2Id = $mobileInfo2->getId();

        // 重新获取实体确保它在 EntityManager 上下文中
        $managedMobileInfo2 = self::getEntityManager()->find(MobileInfo::class, $mobileInfo2Id);
        $this->assertNotNull($managedMobileInfo2);

        $repository->remove($managedMobileInfo2, true);

        // 验证实体直接从数据库删除
        $this->assertEntityNotExists(MobileInfo::class, $mobileInfo2Id);
    }

    // ======== findOneBy 排序逻辑测试 ========

    public function testFindOneByWithOrderByAscDesc(): void
    {
        $repository = $this->getRepository();

        // 清空现有数据确保测试准确性
        $existingMobileInfos = $repository->findAll();
        foreach ($existingMobileInfos as $mobileInfo) {
            $repository->remove($mobileInfo, false);
        }
        self::getEntityManager()->flush();

        // 创建测试数据
        $account = $this->createTestAccount();
        $userB = $this->createTestUser($account, 'test_user_order_b2');
        $userA = $this->createTestUser($account, 'test_user_order_a2');
        $userC = $this->createTestUser($account, 'test_user_order_c2');
        $this->createTestMobileInfo($userB, '86', '13800138141', 'encrypted_b2');
        $this->createTestMobileInfo($userA, '86', '13800138140', 'encrypted_a2');
        $this->createTestMobileInfo($userC, '86', '13800138142', 'encrypted_c2');

        // 执行测试 - 按手机号升序排列，应该返回第一个
        $result = $repository->findOneBy([], ['mobileDecrypted' => 'ASC']);

        // 验证结果
        $this->assertInstanceOf(MobileInfo::class, $result);
        $this->assertEquals('13800138140', $result->getMobileDecrypted());

        // 执行测试 - 按手机号降序排列，应该返回最后一个
        $result = $repository->findOneBy([], ['mobileDecrypted' => 'DESC']);

        // 验证结果
        $this->assertInstanceOf(MobileInfo::class, $result);
        $this->assertEquals('13800138142', $result->getMobileDecrypted());
    }

    public function testFindOneByOrderByMobileDecryptedAsc(): void
    {
        $repository = $this->getRepository();

        // 清空现有数据确保测试准确性
        $existingMobileInfos = $repository->findAll();
        foreach ($existingMobileInfos as $mobileInfo) {
            $repository->remove($mobileInfo, false);
        }
        self::getEntityManager()->flush();

        // 创建测试数据
        $account = $this->createTestAccount();
        $userZ = $this->createTestUser($account, 'test_user_z_asc');
        $userA = $this->createTestUser($account, 'test_user_a_asc');
        $this->createTestMobileInfo($userZ, '86', '13800138152', 'encrypted_z_asc');
        $this->createTestMobileInfo($userA, '86', '13800138150', 'encrypted_a_asc');

        // 执行测试 - 使用 findOneBy 按手机号升序排列
        $result = $repository->findOneBy([], ['mobileDecrypted' => 'ASC']);

        // 验证结果
        $this->assertInstanceOf(MobileInfo::class, $result);
        $this->assertEquals('13800138150', $result->getMobileDecrypted());
    }

    public function testFindOneByOrderByMobileDecryptedDesc(): void
    {
        $repository = $this->getRepository();

        // 清空现有数据确保测试准确性
        $existingMobileInfos = $repository->findAll();
        foreach ($existingMobileInfos as $mobileInfo) {
            $repository->remove($mobileInfo, false);
        }
        self::getEntityManager()->flush();

        // 创建测试数据
        $account = $this->createTestAccount();
        $userA = $this->createTestUser($account, 'test_user_a_desc');
        $userZ = $this->createTestUser($account, 'test_user_z_desc');
        $this->createTestMobileInfo($userA, '86', '13800138160', 'encrypted_a_desc');
        $this->createTestMobileInfo($userZ, '86', '13800138162', 'encrypted_z_desc');

        // 执行测试 - 使用 findOneBy 按手机号降序排列
        $result = $repository->findOneBy([], ['mobileDecrypted' => 'DESC']);

        // 验证结果
        $this->assertInstanceOf(MobileInfo::class, $result);
        $this->assertEquals('13800138162', $result->getMobileDecrypted());
    }

    // ======== 可空字段 IS NULL 查询测试 ========

    public function testFindByIsNull(): void
    {
        $repository = $this->getRepository();

        // 测试可空字段的 IS NULL 查询
        $results = $repository->findBy(['countryCode' => null]);
        $this->assertIsArray($results);

        $results = $repository->findBy(['mobileDecrypted' => null]);
        $this->assertIsArray($results);

        $results = $repository->findBy(['mobileEncrypted' => null]);
        $this->assertIsArray($results);
    }

    public function testCountIsNull(): void
    {
        $repository = $this->getRepository();

        // 测试可空字段的 count IS NULL 查询
        $count = $repository->count(['countryCode' => null]);
        $this->assertGreaterThanOrEqual(0, $count);

        $count = $repository->count(['mobileDecrypted' => null]);
        $this->assertGreaterThanOrEqual(0, $count);

        $count = $repository->count(['mobileEncrypted' => null]);
        $this->assertGreaterThanOrEqual(0, $count);
    }

    public function testFindByWithNullCountryCode(): void
    {
        $repository = $this->getRepository();

        // 创建测试数据（countryCode 为 null）
        $account = $this->createTestAccount();
        $user = $this->createTestUser($account, 'test_user_null_country');
        $mobileInfo = new MobileInfo();
        $mobileInfo->setUser($user);
        $mobileInfo->setCountryCode(null);
        $mobileInfo->setMobileDecrypted('13800138170');
        $mobileInfo->setMobileEncrypted('encrypted_170');

        $entityManager = self::getEntityManager();
        $entityManager->persist($mobileInfo);
        $entityManager->flush();

        // 执行测试 - 查找 countryCode 为 null 的记录
        $results = $repository->findBy(['countryCode' => null]);

        // 验证结果
        $this->assertIsArray($results);
        $this->assertGreaterThanOrEqual(1, count($results));
    }

    public function testFindByWithNullMobileDecrypted(): void
    {
        $repository = $this->getRepository();

        // 创建测试数据（mobileDecrypted 为 null）
        $account = $this->createTestAccount();
        $user = $this->createTestUser($account, 'test_user_null_mobile_decrypted');
        $mobileInfo = new MobileInfo();
        $mobileInfo->setUser($user);
        $mobileInfo->setCountryCode('86');
        $mobileInfo->setMobileDecrypted(null);
        $mobileInfo->setMobileEncrypted('encrypted_180');

        $entityManager = self::getEntityManager();
        $entityManager->persist($mobileInfo);
        $entityManager->flush();

        // 执行测试 - 查找 mobileDecrypted 为 null 的记录
        $results = $repository->findBy(['mobileDecrypted' => null]);

        // 验证结果
        $this->assertIsArray($results);
        $this->assertGreaterThanOrEqual(1, count($results));
    }

    public function testFindByWithNullMobileEncrypted(): void
    {
        $repository = $this->getRepository();

        // 创建测试数据（mobileEncrypted 为 null）
        $account = $this->createTestAccount();
        $user = $this->createTestUser($account, 'test_user_null_mobile_encrypted');
        $mobileInfo = new MobileInfo();
        $mobileInfo->setUser($user);
        $mobileInfo->setCountryCode('86');
        $mobileInfo->setMobileDecrypted('13800138190');
        $mobileInfo->setMobileEncrypted(null);

        $entityManager = self::getEntityManager();
        $entityManager->persist($mobileInfo);
        $entityManager->flush();

        // 执行测试 - 查找 mobileEncrypted 为 null 的记录
        $results = $repository->findBy(['mobileEncrypted' => null]);

        // 验证结果
        $this->assertIsArray($results);
        $this->assertGreaterThanOrEqual(1, count($results));
    }

    public function testCountWithNullCountryCode(): void
    {
        $repository = $this->getRepository();

        // 执行测试 - 计算 countryCode 为 null 的记录数量
        $count = $repository->count(['countryCode' => null]);

        // 验证结果 - 应该是一个非负整数
        $this->assertGreaterThanOrEqual(0, $count);
    }

    // ======== 无效字段查询健壮性测试 ========

    // ======== 关联查询测试 ========

    public function testFindByUser(): void
    {
        $repository = $this->getRepository();

        // 创建测试数据
        $account = $this->createTestAccount();
        $user = $this->createTestUser($account, 'test_user_association');
        $mobileInfo = $this->createTestMobileInfo($user, '86', '13800138200', 'encrypted_200');

        // 执行测试 - 通过用户查找手机信息
        $result = $repository->findOneBy(['user' => $user]);

        // 验证结果
        $this->assertInstanceOf(MobileInfo::class, $result);
        $this->assertEquals($user->getId(), $result->getUser()->getId());
        $this->assertEquals('13800138200', $result->getMobileDecrypted());
    }

    public function testFindWithUserAssociation(): void
    {
        $repository = $this->getRepository();

        // 创建测试数据
        $account = $this->createTestAccount();
        $user = $this->createTestUser($account, 'test_user_find_association');
        $mobileInfo = $this->createTestMobileInfo($user, '86', '13800138210', 'encrypted_210');
        $mobileInfoId = $mobileInfo->getId();

        // 清理缓存
        self::getEntityManager()->clear();

        // 执行测试 - 查找并验证关联
        $result = $repository->find($mobileInfoId);

        // 验证结果
        $this->assertInstanceOf(MobileInfo::class, $result);
        $this->assertInstanceOf(User::class, $result->getUser());
        $this->assertEquals($user->getId(), $result->getUser()->getId());
    }

    public function testFindByWithUserAssociation(): void
    {
        $repository = $this->getRepository();

        // 创建测试数据
        $account = $this->createTestAccount();
        $user1 = $this->createTestUser($account, 'test_user_assoc_1');
        $user2 = $this->createTestUser($account, 'test_user_assoc_2');
        $this->createTestMobileInfo($user1, '86', '13800138220', 'encrypted_220');
        $this->createTestMobileInfo($user2, '86', '13800138221', 'encrypted_221');

        // 执行测试 - 通过用户 ID 查找手机信息
        $results = $repository->findBy(['user' => $user1]);

        // 验证结果
        $this->assertIsArray($results);
        $this->assertCount(1, $results);
        $this->assertEquals($user1->getId(), $results[0]->getUser()->getId());
    }

    public function testCountByUser(): void
    {
        $repository = $this->getRepository();

        // 创建测试数据
        $account = $this->createTestAccount();
        $user1 = $this->createTestUser($account, 'test_user_count_assoc_1');
        $user2 = $this->createTestUser($account, 'test_user_count_assoc_2');
        $this->createTestMobileInfo($user1, '86', '13800138230', 'encrypted_230');
        $this->createTestMobileInfo($user2, '86', '13800138231', 'encrypted_231');

        // 执行测试 - 统计用户的手机信息数量
        $count = $repository->count(['user' => $user1]);

        // 验证结果
        $this->assertEquals(1, $count);
    }

    public function testFindOneByAssociationUserShouldReturnMatchingEntity(): void
    {
        $repository = $this->getRepository();

        // 创建测试数据
        $account = $this->createTestAccount();
        $user = $this->createTestUser($account, 'test_user_find_one_assoc');
        $mobileInfo = $this->createTestMobileInfo($user, '86', '13800138250', 'encrypted_250');

        // 执行测试 - 通过用户查找手机信息
        $result = $repository->findOneBy(['user' => $user]);

        // 验证结果
        $this->assertInstanceOf(MobileInfo::class, $result);
        $this->assertEquals($user->getId(), $result->getUser()->getId());
        $this->assertEquals('13800138250', $result->getMobileDecrypted());
    }

    public function testCountByAssociationUserShouldReturnCorrectNumber(): void
    {
        $repository = $this->getRepository();

        // 创建测试数据
        $account = $this->createTestAccount();
        $user1 = $this->createTestUser($account, 'test_user_count_assoc_specific_1');
        $user2 = $this->createTestUser($account, 'test_user_count_assoc_specific_2');
        $this->createTestMobileInfo($user1, '86', '13800138260', 'encrypted_260');
        $this->createTestMobileInfo($user2, '86', '13800138261', 'encrypted_261');

        // 执行测试 - 统计特定用户的手机信息数量
        $count = $repository->count(['user' => $user1]);

        // 验证结果
        $this->assertEquals(1, $count);
    }
}
