<?php

namespace YouzanApiUserBundle\Tests\Repository;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\RunTestsInSeparateProcesses;
use Tourze\PHPUnitSymfonyKernelTest\AbstractRepositoryTestCase;
use YouzanApiBundle\Entity\Account;
use YouzanApiUserBundle\Entity\User;
use YouzanApiUserBundle\Entity\WechatInfo;
use YouzanApiUserBundle\Enum\FansStatusEnum;
use YouzanApiUserBundle\Enum\WechatTypeEnum;
use YouzanApiUserBundle\Repository\WechatInfoRepository;

/**
 * @internal
 */
#[CoversClass(WechatInfoRepository::class)]
#[RunTestsInSeparateProcesses]
final class WechatInfoRepositoryTest extends AbstractRepositoryTestCase
{
    private WechatInfoRepository $repository;

    protected function onSetUp(): void
    {
        $this->repository = self::getService(WechatInfoRepository::class);
    }

    public function testCanBeInstantiated(): void
    {
        $this->assertInstanceOf(WechatInfoRepository::class, $this->repository);
    }

    public function testFind(): void
    {
        $user = $this->createTestUser('test_yz_open_id_1');
        $wechatInfo = $this->createWechatInfo($user, 'union_id_1');

        $this->persistEntities([$user, $wechatInfo]);

        $result = $this->repository->find($wechatInfo->getId());

        $this->assertInstanceOf(WechatInfo::class, $result);
        $this->assertSame($wechatInfo->getId(), $result->getId());
    }

    public function testFindAll(): void
    {
        $user1 = $this->createTestUser('test_yz_open_id_2');
        $user2 = $this->createTestUser('test_yz_open_id_3');
        $wechatInfo1 = $this->createWechatInfo($user1, 'union_id_2');
        $wechatInfo2 = $this->createWechatInfo($user2, 'union_id_3');

        $this->persistEntities([$user1, $user2, $wechatInfo1, $wechatInfo2]);

        $result = $this->repository->findAll();

        $this->assertIsArray($result);
        $this->assertGreaterThanOrEqual(2, count($result));
        $this->assertContainsOnlyInstancesOf(WechatInfo::class, $result);
    }

    public function testFindBy(): void
    {
        $user = $this->createTestUser('test_yz_open_id_4');
        $wechatInfo = $this->createWechatInfo($user, 'union_id_4', FansStatusEnum::FOLLOWED);

        $this->persistEntities([$user, $wechatInfo]);

        $result = $this->repository->findBy(['fansStatus' => FansStatusEnum::FOLLOWED]);

        $this->assertIsArray($result);
        $this->assertGreaterThanOrEqual(1, count($result));
        $this->assertContainsOnlyInstancesOf(WechatInfo::class, $result);

        foreach ($result as $item) {
            $this->assertSame(FansStatusEnum::FOLLOWED, $item->getFansStatus());
        }
    }

    public function testFindByWithLimit(): void
    {
        $user1 = $this->createTestUser('test_yz_open_id_7');
        $user2 = $this->createTestUser('test_yz_open_id_8');
        $wechatInfo1 = $this->createWechatInfo($user1, 'union_id_7', FansStatusEnum::FOLLOWED);
        $wechatInfo2 = $this->createWechatInfo($user2, 'union_id_8', FansStatusEnum::FOLLOWED);

        $this->persistEntities([$user1, $user2, $wechatInfo1, $wechatInfo2]);

        $result = $this->repository->findBy(
            ['fansStatus' => FansStatusEnum::FOLLOWED],
            null,
            1
        );

        $this->assertIsArray($result);
        $this->assertCount(1, $result);
        $this->assertInstanceOf(WechatInfo::class, $result[0]);
    }

    public function testFindByWithOffset(): void
    {
        $user1 = $this->createTestUser('test_yz_open_id_9');
        $user2 = $this->createTestUser('test_yz_open_id_10');
        $wechatInfo1 = $this->createWechatInfo($user1, 'union_id_9', FansStatusEnum::FOLLOWED);
        $wechatInfo2 = $this->createWechatInfo($user2, 'union_id_10', FansStatusEnum::FOLLOWED);

        $this->persistEntities([$user1, $user2, $wechatInfo1, $wechatInfo2]);

        $allResults = $this->repository->findBy(['fansStatus' => FansStatusEnum::FOLLOWED]);
        $offsetResults = $this->repository->findBy(
            ['fansStatus' => FansStatusEnum::FOLLOWED],
            null,
            null,
            1
        );

        $this->assertIsArray($offsetResults);
        $this->assertGreaterThanOrEqual(1, count($allResults));
        $this->assertLessThanOrEqual(count($allResults) - 1, count($offsetResults));
    }

    public function testFindByEmpty(): void
    {
        $result = $this->repository->findBy(['unionId' => 'non_existent_union_id']);

        $this->assertIsArray($result);
        $this->assertEmpty($result);
    }

    public function testFindOneBy(): void
    {
        $user = $this->createTestUser('test_yz_open_id_11');
        $wechatInfo = $this->createWechatInfo($user, 'union_id_11');

        $this->persistEntities([$user, $wechatInfo]);

        $result = $this->repository->findOneBy(['unionId' => 'union_id_11']);

        $this->assertInstanceOf(WechatInfo::class, $result);
        $this->assertSame('union_id_11', $result->getUnionId());
    }

    public function testFindOneByWithOrderBy(): void
    {
        $user1 = $this->createTestUser('test_yz_open_id_12');
        $user2 = $this->createTestUser('test_yz_open_id_13');
        $wechatInfo1 = $this->createWechatInfo($user1, 'union_id_12', FansStatusEnum::FOLLOWED);
        $wechatInfo2 = $this->createWechatInfo($user2, 'union_id_13', FansStatusEnum::FOLLOWED);

        $this->persistEntities([$user1, $user2, $wechatInfo1, $wechatInfo2]);

        $result = $this->repository->findOneBy(
            ['fansStatus' => FansStatusEnum::FOLLOWED],
            ['id' => 'DESC']
        );

        $this->assertInstanceOf(WechatInfo::class, $result);
        $this->assertSame(FansStatusEnum::FOLLOWED, $result->getFansStatus());
    }

    public function testFindOneByReturnsNullForNonExistent(): void
    {
        $result = $this->repository->findOneBy(['unionId' => 'non_existent_union_id']);

        $this->assertNull($result);
    }

    public function testFindByUnionId(): void
    {
        $user = $this->createTestUser('test_yz_open_id_14');
        $wechatInfo = $this->createWechatInfo($user, 'union_id_14');

        $this->persistEntities([$user, $wechatInfo]);

        $result = $this->repository->findByUnionId('union_id_14');

        $this->assertInstanceOf(WechatInfo::class, $result);
        $this->assertSame('union_id_14', $result->getUnionId());
    }

    public function testFindByUnionIdReturnsNullForNonExistent(): void
    {
        $result = $this->repository->findByUnionId('non_existent_union_id');

        $this->assertNull($result);
    }

    public function testFindAllFans(): void
    {
        $user1 = $this->createTestUser('test_yz_open_id_15');
        $user2 = $this->createTestUser('test_yz_open_id_16');
        $user3 = $this->createTestUser('test_yz_open_id_17');
        $followedWechatInfo = $this->createWechatInfo($user1, 'union_id_15', FansStatusEnum::FOLLOWED);
        $unfollowedWechatInfo = $this->createWechatInfo($user2, 'union_id_16', FansStatusEnum::UNFOLLOWED);
        $silentAuthWechatInfo = $this->createWechatInfo($user3, 'union_id_17', FansStatusEnum::SILENT_AUTH);

        $this->persistEntities([$user1, $user2, $user3, $followedWechatInfo, $unfollowedWechatInfo, $silentAuthWechatInfo]);

        $result = $this->repository->findAllFans();

        $this->assertIsArray($result);
        $this->assertGreaterThanOrEqual(1, count($result));
        $this->assertContainsOnlyInstancesOf(WechatInfo::class, $result);

        foreach ($result as $wechatInfo) {
            $this->assertSame(FansStatusEnum::FOLLOWED, $wechatInfo->getFansStatus());
        }
    }

    public function testFindAllFansReturnsEmptyWhenNoFans(): void
    {
        $user = $this->createTestUser('test_yz_open_id_18');
        $wechatInfo = $this->createWechatInfo($user, 'union_id_18', FansStatusEnum::UNFOLLOWED);

        $this->persistEntities([$user, $wechatInfo]);

        self::getEntityManager()->clear();
        self::getEntityManager()->createQuery('DELETE FROM ' . WechatInfo::class . ' w WHERE w.fansStatus = :status')
            ->setParameter('status', FansStatusEnum::FOLLOWED)
            ->execute()
        ;

        $result = $this->repository->findAllFans();

        $this->assertIsArray($result);
        $this->assertEmpty($result);
    }

    public function testSave(): void
    {
        $user = $this->createTestUser('test_yz_open_id_19');
        $wechatInfo = $this->createWechatInfo($user, 'union_id_19');

        $this->persistEntities([$user]);

        $this->repository->save($wechatInfo);

        $this->assertGreaterThan(0, $wechatInfo->getId());

        $savedWechatInfo = $this->repository->find($wechatInfo->getId());
        $this->assertInstanceOf(WechatInfo::class, $savedWechatInfo);
        $this->assertSame('union_id_19', $savedWechatInfo->getUnionId());
    }

    public function testSaveWithoutFlush(): void
    {
        $user = $this->createTestUser('test_yz_open_id_20');
        $wechatInfo = $this->createWechatInfo($user, 'union_id_20');

        $this->persistEntities([$user]);

        $this->repository->save($wechatInfo, false);
        self::getEntityManager()->flush();

        $this->assertGreaterThan(0, $wechatInfo->getId());

        $savedWechatInfo = $this->repository->find($wechatInfo->getId());
        $this->assertInstanceOf(WechatInfo::class, $savedWechatInfo);
        $this->assertSame('union_id_20', $savedWechatInfo->getUnionId());
    }

    public function testRemove(): void
    {
        $user = $this->createTestUser('test_yz_open_id_21');
        $wechatInfo = $this->createWechatInfo($user, 'union_id_21');

        $this->persistEntities([$user, $wechatInfo]);

        $wechatInfoId = $wechatInfo->getId();

        $this->repository->remove($wechatInfo);

        $removedWechatInfo = $this->repository->find($wechatInfoId);
        $this->assertNull($removedWechatInfo);
    }

    public function testFindByWithMultipleCriteria(): void
    {
        $user = $this->createTestUser('test_yz_open_id_23');
        $wechatInfo = $this->createWechatInfo($user, 'union_id_23', FansStatusEnum::FOLLOWED, WechatTypeEnum::OFFICIAL_ACCOUNT);

        $this->persistEntities([$user, $wechatInfo]);

        $result = $this->repository->findBy([
            'fansStatus' => FansStatusEnum::FOLLOWED,
            'wechatType' => WechatTypeEnum::OFFICIAL_ACCOUNT,
        ]);

        $this->assertIsArray($result);
        $this->assertGreaterThanOrEqual(1, count($result));
        $this->assertContainsOnlyInstancesOf(WechatInfo::class, $result);

        foreach ($result as $item) {
            $this->assertSame(FansStatusEnum::FOLLOWED, $item->getFansStatus());
            $this->assertSame(WechatTypeEnum::OFFICIAL_ACCOUNT, $item->getWechatType());
        }
    }

    public function testFindByWithNullCriteria(): void
    {
        $user = $this->createTestUser('test_yz_open_id_24');
        $wechatInfo = $this->createWechatInfo($user, 'union_id_24');
        $wechatInfo->setWechatType(null);

        $this->persistEntities([$user, $wechatInfo]);

        $result = $this->repository->findBy(['wechatType' => null]);

        $this->assertIsArray($result);
        $this->assertGreaterThanOrEqual(1, count($result));
        $this->assertContainsOnlyInstancesOf(WechatInfo::class, $result);

        foreach ($result as $item) {
            $this->assertNull($item->getWechatType());
        }
    }

    public function testFindByAssociation(): void
    {
        $user = $this->createTestUser('test_yz_open_id_25');
        $wechatInfo = $this->createWechatInfo($user, 'union_id_25');

        $this->persistEntities([$user, $wechatInfo]);

        $result = $this->repository->findBy(['user' => $user]);

        $this->assertIsArray($result);
        $this->assertCount(1, $result);
        $this->assertInstanceOf(WechatInfo::class, $result[0]);
        $this->assertSame($user->getId(), $result[0]->getUser()->getId());
    }

    private function createTestUser(string $yzOpenId): User
    {
        $account = new Account();
        $account->setName('Test Account');
        $account->setClientId('test_client_id_' . $yzOpenId);
        $account->setClientSecret('test_client_secret');

        self::getEntityManager()->persist($account);

        $user = new User();
        $user->setYzOpenId($yzOpenId);
        $user->setNickNameDecrypted('Test User');
        $user->setAccount($account);

        return $user;
    }

    private function createWechatInfo(
        User $user,
        string $unionId,
        FansStatusEnum $fansStatus = FansStatusEnum::UNFOLLOWED,
        ?WechatTypeEnum $wechatType = WechatTypeEnum::OFFICIAL_ACCOUNT,
    ): WechatInfo {
        $wechatInfo = new WechatInfo();
        $wechatInfo->setUser($user);
        $wechatInfo->setUnionId($unionId);
        $wechatInfo->setFansStatus($fansStatus);
        $wechatInfo->setWechatType($wechatType);

        return $wechatInfo;
    }

    public function testFindOneByWithOrderByClause(): void
    {
        $uniqueSuffix = uniqid();
        $user1 = $this->createTestUser('test_yz_open_id_order_one_1_' . $uniqueSuffix);
        $user2 = $this->createTestUser('test_yz_open_id_order_one_2_' . $uniqueSuffix);
        $wechatInfo1 = $this->createWechatInfo($user1, 'union_id_z_one_' . $uniqueSuffix, FansStatusEnum::SILENT_AUTH, WechatTypeEnum::MINI_PROGRAM);
        $wechatInfo2 = $this->createWechatInfo($user2, 'union_id_a_one_' . $uniqueSuffix, FansStatusEnum::SILENT_AUTH, WechatTypeEnum::MINI_PROGRAM);

        $this->persistEntities([$user1, $user2, $wechatInfo1, $wechatInfo2]);

        $allResults = $this->repository->findBy(['fansStatus' => FansStatusEnum::SILENT_AUTH, 'wechatType' => WechatTypeEnum::MINI_PROGRAM], ['unionId' => 'ASC']);
        $result = $this->repository->findOneBy(['fansStatus' => FansStatusEnum::SILENT_AUTH, 'wechatType' => WechatTypeEnum::MINI_PROGRAM], ['unionId' => 'ASC']);

        $this->assertInstanceOf(WechatInfo::class, $result);
        if (count($allResults) >= 2) {
            $this->assertSame($allResults[0]->getUnionId(), $result->getUnionId());
        } else {
            $unionId = $result->getUnionId();
            $this->assertNotNull($unionId);
            $this->assertStringContainsString('union_id_', $unionId);
        }
    }

    public function testFindByWithAssociationCriteria(): void
    {
        $user = $this->createTestUser('test_yz_open_id_assoc');
        $wechatInfo = $this->createWechatInfo($user, 'union_id_assoc');

        $this->persistEntities([$user, $wechatInfo]);

        $result = $this->repository->findBy(['user' => $user]);

        $this->assertIsArray($result);
        $this->assertCount(1, $result);
        foreach ($result as $item) {
            $this->assertInstanceOf(WechatInfo::class, $item);
            $this->assertSame($user->getId(), $item->getUser()->getId());
        }
    }

    public function testCountByWithAssociationCriteria(): void
    {
        $user = $this->createTestUser('test_yz_open_id_count_assoc');
        $wechatInfo = $this->createWechatInfo($user, 'union_id_count_assoc');

        $this->persistEntities([$user, $wechatInfo]);

        $count = $this->repository->count(['user' => $user]);

        $this->assertSame(1, $count);
    }

    public function testFindByWithNullWechatType(): void
    {
        $user = $this->createTestUser('test_yz_open_id_null_type');
        $wechatInfo = $this->createWechatInfo($user, 'union_id_null_type');
        $wechatInfo->setWechatType(null);

        $this->persistEntities([$user, $wechatInfo]);

        $result = $this->repository->findBy(['wechatType' => null]);

        $this->assertIsArray($result);
        $this->assertGreaterThanOrEqual(1, count($result));
        foreach ($result as $item) {
            $this->assertNull($item->getWechatType());
        }
    }

    public function testCountByWithNullWechatType(): void
    {
        $initialCount = $this->repository->count(['wechatType' => null]);

        $user = $this->createTestUser('test_yz_open_id_count_null_type');
        $wechatInfo = $this->createWechatInfo($user, 'union_id_count_null_type');
        $wechatInfo->setWechatType(null);

        $this->persistEntities([$user, $wechatInfo]);

        $count = $this->repository->count(['wechatType' => null]);

        $this->assertSame($initialCount + 1, $count);
    }

    public function testFindByWithNullFollowTime(): void
    {
        $user = $this->createTestUser('test_yz_open_id_null_follow');
        $wechatInfo = $this->createWechatInfo($user, 'union_id_null_follow');
        $wechatInfo->setFollowTime(null);

        $this->persistEntities([$user, $wechatInfo]);

        $result = $this->repository->findBy(['followTime' => null]);

        $this->assertIsArray($result);
        $this->assertGreaterThanOrEqual(1, count($result));
        foreach ($result as $item) {
            $this->assertNull($item->getFollowTime());
        }
    }

    public function testFindByWithNullLastTalkTime(): void
    {
        $user = $this->createTestUser('test_yz_open_id_null_talk');
        $wechatInfo = $this->createWechatInfo($user, 'union_id_null_talk');
        $wechatInfo->setLastTalkTime(null);

        $this->persistEntities([$user, $wechatInfo]);

        $result = $this->repository->findBy(['lastTalkTime' => null]);

        $this->assertIsArray($result);
        $this->assertGreaterThanOrEqual(1, count($result));
        foreach ($result as $item) {
            $this->assertNull($item->getLastTalkTime());
        }
    }

    public function testCountByWithNullFollowTime(): void
    {
        $initialCount = $this->repository->count(['followTime' => null]);

        $user = $this->createTestUser('test_yz_open_id_count_null_follow');
        $wechatInfo = $this->createWechatInfo($user, 'union_id_count_null_follow');
        $wechatInfo->setFollowTime(null);

        $this->persistEntities([$user, $wechatInfo]);

        $count = $this->repository->count(['followTime' => null]);

        $this->assertSame($initialCount + 1, $count);
    }

    public function testFindOneByWithSortingLogic(): void
    {
        $uniquePrefix = 'test_sort_' . uniqid();
        $user1 = $this->createTestUser($uniquePrefix . '_user_1');
        $user2 = $this->createTestUser($uniquePrefix . '_user_2');
        $wechatInfo1 = $this->createWechatInfo($user1, $uniquePrefix . '_z_union', FansStatusEnum::FOLLOWED);
        $wechatInfo2 = $this->createWechatInfo($user2, $uniquePrefix . '_a_union', FansStatusEnum::FOLLOWED);

        $this->persistEntities([$user1, $user2, $wechatInfo1, $wechatInfo2]);

        $allSortResults = $this->repository->findBy(
            ['unionId' => [$uniquePrefix . '_z_union', $uniquePrefix . '_a_union']],
            ['unionId' => 'ASC']
        );

        $this->assertIsArray($allSortResults);
        $this->assertCount(2, $allSortResults);
        $this->assertSame($uniquePrefix . '_a_union', $allSortResults[0]->getUnionId());
        $this->assertSame($uniquePrefix . '_z_union', $allSortResults[1]->getUnionId());
    }

    public function testCountByAssociationField(): void
    {
        $user = $this->createTestUser('test_yz_open_id_count_assoc_field');
        $wechatInfo = $this->createWechatInfo($user, 'union_id_count_assoc_field');

        $this->persistEntities([$user, $wechatInfo]);

        $count = $this->repository->count(['user' => $user]);
        $this->assertSame(1, $count);
    }

    public function testFindByAssociationField(): void
    {
        $user = $this->createTestUser('test_yz_open_id_find_assoc_field');
        $wechatInfo = $this->createWechatInfo($user, 'union_id_find_assoc_field');

        $this->persistEntities([$user, $wechatInfo]);

        $result = $this->repository->findBy(['user' => $user]);
        $this->assertIsArray($result);
        $this->assertCount(1, $result);
        foreach ($result as $foundWechatInfo) {
            $this->assertSame($user->getId(), $foundWechatInfo->getUser()->getId());
        }
    }

    public function testFindByNullableFieldIsNull(): void
    {
        $user = $this->createTestUser('test_yz_open_id_null_check');
        $wechatInfo = $this->createWechatInfo($user, 'union_id_null_check');
        $wechatInfo->setFollowTime(null);

        $this->persistEntities([$user, $wechatInfo]);

        $result = $this->repository->findBy(['followTime' => null]);
        $this->assertIsArray($result);
        $this->assertGreaterThanOrEqual(1, count($result));
        foreach ($result as $foundWechatInfo) {
            $this->assertNull($foundWechatInfo->getFollowTime());
        }
    }

    public function testCountByNullableFieldIsNull(): void
    {
        $initialCount = $this->repository->count(['lastTalkTime' => null]);

        $user = $this->createTestUser('test_yz_open_id_count_null_check');
        $wechatInfo = $this->createWechatInfo($user, 'union_id_count_null_check');
        $wechatInfo->setLastTalkTime(null);

        $this->persistEntities([$user, $wechatInfo]);

        $count = $this->repository->count(['lastTalkTime' => null]);
        $this->assertSame($initialCount + 1, $count);
    }

    protected function createNewEntity(): object
    {
        $user = $this->createTestUser('test_yz_open_id_' . uniqid());
        $wechatInfo = new WechatInfo();
        $wechatInfo->setUser($user);
        $wechatInfo->setFansStatus(FansStatusEnum::FOLLOWED);

        return $wechatInfo;
    }

    protected function getRepository(): WechatInfoRepository
    {
        return $this->repository;
    }
}
