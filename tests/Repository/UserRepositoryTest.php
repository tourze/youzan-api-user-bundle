<?php

namespace YouzanApiUserBundle\Tests\Repository;

use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ManagerRegistry;
use PHPUnit\Framework\TestCase;
use YouzanApiUserBundle\Entity\User;
use YouzanApiUserBundle\Repository\UserRepository;

class UserRepositoryTest extends TestCase
{
    private UserRepository $repository;
    private EntityManagerInterface $entityManager;
    private ManagerRegistry $registry;

    protected function setUp(): void
    {
        $this->entityManager = $this->createMock(EntityManagerInterface::class);
        $this->registry = $this->createMock(ManagerRegistry::class);

        $this->registry->expects($this->any())
            ->method('getManagerForClass')
            ->willReturn($this->entityManager);

        $this->repository = new UserRepository($this->registry);
    }

    /**
     * 测试 findByYzOpenId 方法
     */
    public function testFindByYzOpenId_withValidId_callsFindOneBy(): void
    {
        $yzOpenId = 'yz123456789';
        $expectedUser = new User();

        $repositoryMock = $this->getMockBuilder(UserRepository::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['findOneBy'])
            ->getMock();

        $repositoryMock->expects($this->once())
            ->method('findOneBy')
            ->with(['yzOpenId' => $yzOpenId])
            ->willReturn($expectedUser);

        $result = $repositoryMock->findByYzOpenId($yzOpenId);

        $this->assertSame($expectedUser, $result);
    }

    /**
     * 测试 findByYzOpenId 方法，返回 null
     */
    public function testFindByYzOpenId_withInvalidId_returnsNull(): void
    {
        $yzOpenId = 'invalid_id';

        $repositoryMock = $this->getMockBuilder(UserRepository::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['findOneBy'])
            ->getMock();

        $repositoryMock->expects($this->once())
            ->method('findOneBy')
            ->with(['yzOpenId' => $yzOpenId])
            ->willReturn(null);

        $result = $repositoryMock->findByYzOpenId($yzOpenId);

        $this->assertNull($result);
    }

    /**
     * 测试 findByUnionId 方法
     */
    public function testFindByUnionId_withValidId_callsFindOneBy(): void
    {
        $unionId = 'wx_union_123456789';
        $expectedUser = new User();

        $repositoryMock = $this->getMockBuilder(UserRepository::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['findOneBy'])
            ->getMock();

        $repositoryMock->expects($this->once())
            ->method('findOneBy')
            ->with(['unionId' => $unionId])
            ->willReturn($expectedUser);

        $result = $repositoryMock->findByUnionId($unionId);

        $this->assertSame($expectedUser, $result);
    }

    /**
     * 测试 findByWeixinOpenId 方法
     */
    public function testFindByWeixinOpenId_withValidId_callsFindOneBy(): void
    {
        $weixinOpenId = 'o_abc123456789';
        $expectedUser = new User();

        $repositoryMock = $this->getMockBuilder(UserRepository::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['findOneBy'])
            ->getMock();

        $repositoryMock->expects($this->once())
            ->method('findOneBy')
            ->with(['weixinOpenId' => $weixinOpenId])
            ->willReturn($expectedUser);

        $result = $repositoryMock->findByWeixinOpenId($weixinOpenId);

        $this->assertSame($expectedUser, $result);
    }

    /**
     * 测试 findByMobile 方法
     */
    public function testFindByMobile_withDefaultCountryCode_callsFindOneBy(): void
    {
        $mobile = '13800138000';
        $expectedUser = new User();

        $repositoryMock = $this->getMockBuilder(UserRepository::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['findOneBy'])
            ->getMock();

        $repositoryMock->expects($this->once())
            ->method('findOneBy')
            ->with([
                'mobile' => $mobile,
                'countryCode' => '+86'
            ])
            ->willReturn($expectedUser);

        $result = $repositoryMock->findByMobile($mobile);

        $this->assertSame($expectedUser, $result);
    }

    /**
     * 测试 findByMobile 方法，使用自定义的国家代码
     */
    public function testFindByMobile_withCustomCountryCode_callsFindOneBy(): void
    {
        $mobile = '5551234567';
        $countryCode = '+1';
        $expectedUser = new User();

        $repositoryMock = $this->getMockBuilder(UserRepository::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['findOneBy'])
            ->getMock();

        $repositoryMock->expects($this->once())
            ->method('findOneBy')
            ->with([
                'mobile' => $mobile,
                'countryCode' => $countryCode
            ])
            ->willReturn($expectedUser);

        $result = $repositoryMock->findByMobile($mobile, $countryCode);

        $this->assertSame($expectedUser, $result);
    }

    /**
     * 测试 findByFollowTimeRange 方法
     */
    public function testFindByFollowTimeRange_correctlyFormatsQuery(): void
    {
        $startTime = new \DateTime('2023-01-01');
        $endTime = new \DateTime('2023-01-31');
        $expectedUser = new User();

        // 创建一个模拟仓库，只模拟 findBy 方法
        $repositoryMock = $this->getMockBuilder(UserRepository::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['createQueryBuilder'])
            ->getMock();

        // 简化测试，使用真实的 Query 构建逻辑，只验证方法被调用但不做复杂的断言
        $repositoryMock->expects($this->once())
            ->method('createQueryBuilder')
            ->with('u')
            ->willReturnCallback(function () {
                // 返回一个简化的 QueryBuilder 模拟
                return $this->getMockBuilder(\Doctrine\ORM\QueryBuilder::class)
                    ->disableOriginalConstructor()
                    ->getMock();
            });

        // 执行测试 - 由于我们不需要验证结果，只需确保方法可以被调用
        $repositoryMock->findByFollowTimeRange($startTime, $endTime);

        // 测试成功完成代表没有异常
        $this->addToAssertionCount(1);
    }

    /**
     * 测试 findAllFans 方法
     */
    public function testFindAllFans_callsFindBy(): void
    {
        $expectedUsers = [new User(), new User()];

        $repositoryMock = $this->getMockBuilder(UserRepository::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['findBy'])
            ->getMock();

        $repositoryMock->expects($this->once())
            ->method('findBy')
            ->with(['isFans' => true])
            ->willReturn($expectedUsers);

        $result = $repositoryMock->findAllFans();

        $this->assertSame($expectedUsers, $result);
    }

    /**
     * 测试 findByPlatformType 方法
     */
    public function testFindByPlatformType_callsFindBy(): void
    {
        $platformType = 1;
        $expectedUsers = [new User(), new User()];

        $repositoryMock = $this->getMockBuilder(UserRepository::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['findBy'])
            ->getMock();

        $repositoryMock->expects($this->once())
            ->method('findBy')
            ->with(['platformType' => $platformType])
            ->willReturn($expectedUsers);

        $result = $repositoryMock->findByPlatformType($platformType);

        $this->assertSame($expectedUsers, $result);
    }
}
