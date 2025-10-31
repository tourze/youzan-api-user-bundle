<?php

namespace YouzanApiUserBundle\Tests\Repository;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\RunTestsInSeparateProcesses;
use Tourze\PHPUnitSymfonyKernelTest\AbstractRepositoryTestCase;
use YouzanApiBundle\Entity\Account;
use YouzanApiUserBundle\Entity\Staff;
use YouzanApiUserBundle\Entity\User;
use YouzanApiUserBundle\Enum\GenderEnum;
use YouzanApiUserBundle\Repository\StaffRepository;

/**
 * @internal
 */
#[CoversClass(StaffRepository::class)]
#[RunTestsInSeparateProcesses]
final class StaffRepositoryTest extends AbstractRepositoryTestCase
{
    private StaffRepository $repository;

    private Account $testAccount;

    private User $testUser;

    private Staff $testStaff;

    protected function onSetUp(): void
    {
        $this->repository = self::getService(StaffRepository::class);

        // 创建测试数据
        $this->createTestData();
    }

    private function createTestData(): void
    {
        $em = self::getEntityManager();

        // 创建测试账号
        $this->testAccount = new Account();
        $this->testAccount->setName('Test Shop');
        $this->testAccount->setClientId('test_client_id');
        $this->testAccount->setClientSecret('test_client_secret');
        $em->persist($this->testAccount);

        // 创建测试用户
        $this->testUser = new User();
        $this->testUser->setYzOpenId('test_yz_open_id');
        $this->testUser->setNickNameDecrypted('Test User');
        $this->testUser->setGender(GenderEnum::MALE);
        $this->testUser->setPlatformType(1);
        $this->testUser->setAccount($this->testAccount);
        $em->persist($this->testUser);

        // 创建测试员工
        $this->testStaff = new Staff();
        $this->testStaff->setCorpName('Test Corp');
        $this->testStaff->setCorpId('corp_123');
        $this->testStaff->setKdtId(12345);
        $this->testStaff->setEmail('test@example.com');
        $this->testStaff->setName('Test Staff');
        $this->testStaff->setUser($this->testUser);
        $em->persist($this->testStaff);

        $em->flush();
    }

    private function createUserForStaff(string $yzOpenId): User
    {
        $em = self::getEntityManager();

        $user = new User();
        $user->setYzOpenId($yzOpenId);
        $user->setNickNameDecrypted('Test User ' . $yzOpenId);
        $user->setGender(GenderEnum::MALE);
        $user->setPlatformType(1);
        $user->setAccount($this->testAccount);
        $em->persist($user);
        $em->flush();

        return $user;
    }

    public function testCanBeInstantiated(): void
    {
        $repository = self::getService(StaffRepository::class);

        $this->assertInstanceOf(StaffRepository::class, $repository);
    }

    // 测试 find 方法

    // 测试 findAll 方法

    // 测试 findBy 方法

    // 测试 findOneBy 方法

    // 健壮性测试

    // 可空字段 IS NULL 查询测试
    public function testFindByCorpNameIsNull(): void
    {
        // 创建一个没有企业名称的员工
        $em = self::getEntityManager();
        $user = $this->createUserForStaff('corp_null_user');
        $staff = new Staff();
        $staff->setCorpId('corp_null');
        $staff->setKdtId(99999);
        $staff->setUser($user);
        $em->persist($staff);
        $em->flush();

        $result = $this->repository->findBy(['corpName' => null]);

        $this->assertIsArray($result);
        $this->assertGreaterThan(0, count($result));
    }

    public function testFindByEmailIsNull(): void
    {
        // 创建一个没有邮箱的员工
        $em = self::getEntityManager();
        $staff = new Staff();
        $staff->setCorpName('Corp Without Email');
        $staff->setCorpId('corp_no_email');
        $staff->setKdtId(88888);
        $user = $this->createUserForStaff(uniqid('user_'));
        $staff->setUser($user);
        $em->persist($staff);
        $em->flush();

        $result = $this->repository->findBy(['email' => null]);

        $this->assertIsArray($result);
        $this->assertGreaterThan(0, count($result));
    }

    public function testCountCorpNameIsNull(): void
    {
        // 创建一个没有企业名称的员工
        $em = self::getEntityManager();
        $staff = new Staff();
        $staff->setCorpId('corp_count_null');
        $staff->setKdtId(77777);
        $user = $this->createUserForStaff(uniqid('user_'));
        $staff->setUser($user);
        $em->persist($staff);
        $em->flush();

        $count = $this->repository->count(['corpName' => null]);

        $this->assertIsInt($count);
        $this->assertGreaterThanOrEqual(1, $count);
    }

    // 关联查询测试
    public function testFindByUserRelation(): void
    {
        $result = $this->repository->findBy(['user' => $this->testUser]);

        $this->assertIsArray($result);
        $this->assertGreaterThan(0, count($result));
        $this->assertContainsOnlyInstancesOf(Staff::class, $result);
    }

    public function testCountByUserRelation(): void
    {
        $count = $this->repository->count(['user' => $this->testUser]);

        $this->assertIsInt($count);
        $this->assertGreaterThanOrEqual(1, $count);
    }

    // 自定义方法测试
    public function testFindByCorpId(): void
    {
        $result = $this->repository->findByCorpId('corp_123');

        $this->assertIsArray($result);
        $this->assertGreaterThan(0, count($result));
        $this->assertContainsOnlyInstancesOf(Staff::class, $result);

        foreach ($result as $staff) {
            $this->assertEquals('corp_123', $staff->getCorpId());
        }
    }

    public function testFindByKdtId(): void
    {
        $result = $this->repository->findByKdtId(12345);

        $this->assertIsArray($result);
        $this->assertGreaterThan(0, count($result));
        $this->assertContainsOnlyInstancesOf(Staff::class, $result);

        foreach ($result as $staff) {
            $this->assertEquals(12345, $staff->getKdtId());
        }
    }

    public function testFindByEmail(): void
    {
        $result = $this->repository->findByEmail('test@example.com');

        $this->assertInstanceOf(Staff::class, $result);
        $this->assertEquals('test@example.com', $result->getEmail());
    }

    public function testFindByEmailWithNonExistentEmail(): void
    {
        $result = $this->repository->findByEmail('nonexistent@example.com');

        $this->assertNull($result);
    }

    // 测试 save 方法
    public function testSave(): void
    {
        $staff = new Staff();
        $staff->setCorpName('New Corp');
        $staff->setCorpId('new_corp');
        $staff->setKdtId(55555);
        $staff->setEmail('new@example.com');
        $staff->setName('New Staff');
        $user = $this->createUserForStaff(uniqid('user_'));
        $staff->setUser($user);

        $this->repository->save($staff);

        $this->assertNotEquals(0, $staff->getId());

        // 验证已保存到数据库
        $found = $this->repository->find($staff->getId());
        $this->assertInstanceOf(Staff::class, $found);
        $this->assertEquals('new@example.com', $found->getEmail());
    }

    public function testSaveWithoutFlush(): void
    {
        $staff = new Staff();
        $staff->setCorpName('No Flush Corp');
        $staff->setCorpId('no_flush_corp');
        $staff->setKdtId(66666);
        $staff->setEmail('noflush@example.com');
        $staff->setName('No Flush Staff');
        $user = $this->createUserForStaff(uniqid('user_'));
        $staff->setUser($user);

        $this->repository->save($staff, false);

        // 实体应该被持久化但未刷新到数据库
        $em = self::getEntityManager();
        $this->assertTrue($em->contains($staff));

        // 手动刷新
        $em->flush();

        $found = $this->repository->find($staff->getId());
        $this->assertInstanceOf(Staff::class, $found);
    }

    // 测试 remove 方法
    public function testRemove(): void
    {
        $staffId = $this->testStaff->getId();

        $this->repository->remove($this->testStaff);

        // 验证已从数据库中移除
        $found = $this->repository->find($staffId);
        $this->assertNull($found);
    }

    // 添加缺失的测试方法

    public function testFindOneByWithOrderByClause(): void
    {
        // 创建多个员工用于排序测试
        $em = self::getEntityManager();
        for ($i = 1; $i <= 3; ++$i) {
            $staff = new Staff();
            $staff->setCorpName("Sorted Corp {$i}");
            $staff->setCorpId("sorted_corp_{$i}");
            $staff->setKdtId(20000 + $i);
            $staff->setEmail("sorted{$i}@example.com");
            $staff->setName("Sorted Staff {$i}");
            $user = $this->createUserForStaff(uniqid('user_'));
            $staff->setUser($user);
            $em->persist($staff);
        }
        $em->flush();

        $result = $this->repository->findOneBy([], ['name' => 'ASC']);

        $this->assertInstanceOf(Staff::class, $result);
        // 应该返回按名称排序的第一个
        $this->assertEquals('Sorted Staff 1', $result->getName());
    }

    // 更多的可空字段测试
    public function testFindByNameIsNull(): void
    {
        // 创建一个没有名称的员工
        $em = self::getEntityManager();
        $staff = new Staff();
        $staff->setCorpName('Corp Without Name');
        $staff->setCorpId('corp_no_name');
        $staff->setKdtId(77778);
        $user = $this->createUserForStaff(uniqid('user_'));
        $staff->setUser($user);
        $em->persist($staff);
        $em->flush();

        $result = $this->repository->findBy(['name' => null]);

        $this->assertIsArray($result);
        $this->assertGreaterThan(0, count($result));
    }

    public function testFindByKdtIdIsNull(): void
    {
        // 创建一个没有kdtId的员工
        $em = self::getEntityManager();
        $staff = new Staff();
        $staff->setCorpName('Corp Without KdtId');
        $staff->setCorpId('corp_no_kdtid');
        $staff->setEmail('nokdtid@example.com');
        $staff->setName('No KdtId Staff');
        $user = $this->createUserForStaff(uniqid('user_'));
        $staff->setUser($user);
        $em->persist($staff);
        $em->flush();

        $result = $this->repository->findBy(['kdtId' => null]);

        $this->assertIsArray($result);
        $this->assertGreaterThan(0, count($result));
    }

    public function testCountNameIsNull(): void
    {
        // 创建一个没有名称的员工
        $em = self::getEntityManager();
        $staff = new Staff();
        $staff->setCorpName('Corp Count Name Null');
        $staff->setCorpId('corp_count_name_null');
        $staff->setKdtId(77779);
        $user = $this->createUserForStaff(uniqid('user_'));
        $staff->setUser($user);
        $em->persist($staff);
        $em->flush();

        $count = $this->repository->count(['name' => null]);

        $this->assertIsInt($count);
        $this->assertGreaterThanOrEqual(1, $count);
    }

    public function testCountEmailIsNull(): void
    {
        // 创建一个没有邮箱的员工
        $em = self::getEntityManager();
        $staff = new Staff();
        $staff->setCorpName('Corp Count Email Null');
        $staff->setCorpId('corp_count_email_null');
        $staff->setKdtId(77780);
        $staff->setName('Count Email Null Staff');
        $user = $this->createUserForStaff(uniqid('user_'));
        $staff->setUser($user);
        $em->persist($staff);
        $em->flush();

        $count = $this->repository->count(['email' => null]);

        $this->assertIsInt($count);
        $this->assertGreaterThanOrEqual(1, $count);
    }

    public function testCountKdtIdIsNull(): void
    {
        // 创建一个没有kdtId的员工
        $em = self::getEntityManager();
        $staff = new Staff();
        $staff->setCorpName('Corp Count KdtId Null');
        $staff->setCorpId('corp_count_kdtid_null');
        $staff->setEmail('countkdtidnull@example.com');
        $staff->setName('Count KdtId Null Staff');
        $user = $this->createUserForStaff(uniqid('user_'));
        $staff->setUser($user);
        $em->persist($staff);
        $em->flush();

        $count = $this->repository->count(['kdtId' => null]);

        $this->assertIsInt($count);
        $this->assertGreaterThanOrEqual(1, $count);
    }

    public function testFindByCorpIdIsNull(): void
    {
        // 创建一个没有企业ID的员工
        $em = self::getEntityManager();
        $staff = new Staff();
        $staff->setCorpName('Corp Without CorpId');
        $staff->setKdtId(88888);
        $staff->setEmail('nocorpid@example.com');
        $staff->setName('No CorpId Staff');
        $user = $this->createUserForStaff(uniqid('user_'));
        $staff->setUser($user);
        $em->persist($staff);
        $em->flush();

        $result = $this->repository->findBy(['corpId' => null]);

        $this->assertIsArray($result);
        $this->assertGreaterThan(0, count($result));
    }

    public function testCountCorpIdIsNull(): void
    {
        // 创建一个没有企业ID的员工
        $em = self::getEntityManager();
        $staff = new Staff();
        $staff->setCorpName('Corp Count CorpId Null');
        $staff->setKdtId(88889);
        $staff->setEmail('countcorpidnull@example.com');
        $staff->setName('Count CorpId Null Staff');
        $user = $this->createUserForStaff(uniqid('user_'));
        $staff->setUser($user);
        $em->persist($staff);
        $em->flush();

        $count = $this->repository->count(['corpId' => null]);

        $this->assertIsInt($count);
        $this->assertGreaterThanOrEqual(1, $count);
    }

    // PHPStan 要求的特定命名模式测试方法

    public function testCountByAssociationUserShouldReturnCorrectNumber(): void
    {
        // 验证测试用户存在并获取关联的员工数量
        $this->assertNotNull($this->testUser);

        // 创建额外的员工记录关联到同一个用户（虽然实际上是 OneToOne 关系）
        // 但为了测试目的，我们测试现有的关联
        $count = $this->repository->count(['user' => $this->testUser]);

        $this->assertIsInt($count);
        $this->assertGreaterThanOrEqual(1, $count);
    }

    public function testFindOneByAssociationUserShouldReturnMatchingEntity(): void
    {
        $result = $this->repository->findOneBy(['user' => $this->testUser]);

        $this->assertInstanceOf(Staff::class, $result);
        $this->assertEquals($this->testUser->getId(), $result->getUser()->getId());
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

    /**
     * @return Staff
     */
    protected function createNewEntity(): object
    {
        $account = $this->createTestAccount();
        $user = new User();
        $user->setYzOpenId('test_yz_open_id_' . uniqid());
        $user->setAccount($account);

        $staff = new Staff();
        $staff->setUser($user);

        return $staff;
    }

    protected function getRepository(): StaffRepository
    {
        return $this->repository;
    }
}
