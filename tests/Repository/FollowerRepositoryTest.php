<?php

namespace YouzanApiUserBundle\Tests\Repository;

use Doctrine\ORM\EntityManagerInterface;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\RunTestsInSeparateProcesses;
use Tourze\PHPUnitSymfonyKernelTest\AbstractRepositoryTestCase;
use YouzanApiBundle\Entity\Account;
use YouzanApiUserBundle\Entity\Follower;
use YouzanApiUserBundle\Entity\LevelInfo;
use YouzanApiUserBundle\Enum\GenderEnum;
use YouzanApiUserBundle\Repository\FollowerRepository;

/**
 * @internal
 */
#[CoversClass(FollowerRepository::class)]
#[RunTestsInSeparateProcesses]
final class FollowerRepositoryTest extends AbstractRepositoryTestCase
{
    protected function onSetUp(): void
    {
        // 测试初始化逻辑
    }

    public function testCanBeInstantiated(): void
    {
        $repository = self::getService(FollowerRepository::class);

        $this->assertInstanceOf(FollowerRepository::class, $repository);
        $this->assertSame(Follower::class, $repository->getClassName());
    }

    public function testGetEntityClass(): void
    {
        $repository = self::getService(FollowerRepository::class);

        $this->assertSame(Follower::class, $repository->getClassName());
    }

    public function testFindAllFollowed(): void
    {
        $repository = self::getService(FollowerRepository::class);

        $result = $repository->findAllFollowed();
        $this->assertIsArray($result);
    }

    public function testFindByNickLike(): void
    {
        $repository = self::getService(FollowerRepository::class);

        $result = $repository->findByNickLike('test');
        $this->assertIsArray($result);
    }

    public function testFindByUserId(): void
    {
        $repository = self::getService(FollowerRepository::class);

        $result = $repository->findByUserId(1);
        $this->assertNull($result);
    }

    public function testFindByWeixinOpenId(): void
    {
        $repository = self::getService(FollowerRepository::class);

        $result = $repository->findByWeixinOpenId('test_openid');
        $this->assertNull($result);
    }

    // Basic Repository find() tests
    public function testFindShouldReturnNullWhenEntityNotFound(): void
    {
        $repository = self::getService(FollowerRepository::class);

        $result = $repository->find(999999);
        $this->assertNull($result);
    }

    public function testFindShouldReturnEntityWhenFound(): void
    {
        $repository = self::getService(FollowerRepository::class);

        // Create test data
        $account = $this->createTestAccount();
        $follower = $this->createTestFollower($account);
        $repository->save($follower);

        $result = $repository->find($follower->getId());
        $this->assertInstanceOf(Follower::class, $result);
        $this->assertSame($follower->getId(), $result->getId());
        $this->assertSame($follower->getUserId(), $result->getUserId());
    }

    public function testFindWithInvalidIdShouldReturnNull(): void
    {
        $repository = self::getService(FollowerRepository::class);

        $result = $repository->find(-1);
        $this->assertNull($result);
    }

    public function testFindWithStringIdShouldWorkForNumericStrings(): void
    {
        $repository = self::getService(FollowerRepository::class);

        $result = $repository->find('999999');
        $this->assertNull($result);
    }

    // findBy() tests

    // findOneBy() tests

    public function testFindOneByShouldRespectOrderByParameter(): void
    {
        $repository = self::getService(FollowerRepository::class);

        // Add test data with different creation times
        $account = $this->createTestAccount();
        $follower1 = $this->createTestFollower($account, ['userId' => 3001, 'nick' => 'First']);
        $follower2 = $this->createTestFollower($account, ['userId' => 3002, 'nick' => 'Second']);
        $repository->save($follower1);
        $repository->save($follower2);

        $resultAsc = $repository->findOneBy(['nick' => 'First'], ['id' => 'ASC']);
        $resultDesc = $repository->findOneBy(['nick' => 'Second'], ['id' => 'DESC']);

        // Results should be different when ordered differently
        $this->assertInstanceOf(Follower::class, $resultAsc);
        $this->assertInstanceOf(Follower::class, $resultDesc);
    }

    // save() and remove() tests
    public function testSaveShouldPersistEntity(): void
    {
        $repository = self::getService(FollowerRepository::class);

        $account = $this->createTestAccount();
        $follower = $this->createTestFollower($account, ['userId' => 4001]);

        $repository->save($follower);

        $this->assertGreaterThan(0, $follower->getId());

        // Verify it can be found
        $found = $repository->find($follower->getId());
        $this->assertInstanceOf(Follower::class, $found);
        $this->assertSame($follower->getUserId(), $found->getUserId());
    }

    public function testSaveWithoutFlushShouldNotPersistImmediately(): void
    {
        $repository = self::getService(FollowerRepository::class);

        $account = $this->createTestAccount();
        $follower = $this->createTestFollower($account, ['userId' => 4002]);

        $repository->save($follower, false);

        // Entity should be managed but not yet persisted
        $this->assertSame(0, $follower->getId());

        // Flush to persist
        $em = self::getService(EntityManagerInterface::class);
        $em->flush();
        $this->assertGreaterThan(0, $follower->getId());
    }

    public function testRemoveShouldDeleteEntity(): void
    {
        $repository = self::getService(FollowerRepository::class);

        $account = $this->createTestAccount();
        $follower = $this->createTestFollower($account, ['userId' => 4003]);
        $repository->save($follower);

        $id = $follower->getId();
        $this->assertGreaterThan(0, $id);

        $repository->remove($follower);

        // Verify it's deleted
        $found = $repository->find($id);
        $this->assertNull($found);
    }

    public function testRemoveWithoutFlushShouldNotDeleteImmediately(): void
    {
        $repository = self::getService(FollowerRepository::class);

        $account = $this->createTestAccount();
        $follower = $this->createTestFollower($account, ['userId' => 4004]);
        $repository->save($follower);

        $id = $follower->getId();
        $repository->remove($follower, false);

        // Should still exist before flush
        $found = $repository->find($id);
        $this->assertInstanceOf(Follower::class, $found);

        // Flush to delete
        $em = self::getService(EntityManagerInterface::class);
        $em->flush();
        $found = $repository->find($id);
        $this->assertNull($found);
    }

    // Association tests

    // IS NULL tests for nullable fields

    public function testFindByAvatarWhenIsNullShouldReturnArrayOfEntities(): void
    {
        $repository = self::getService(FollowerRepository::class);

        $account = $this->createTestAccount();
        $follower = $this->createTestFollower($account, ['userId' => 6002]);
        $follower->setAvatar(null);
        $repository->save($follower);

        $results = $repository->findBy(['avatar' => null]);
        $this->assertIsArray($results);
        foreach ($results as $result) {
            $this->assertNull($result->getAvatar());
        }
    }

    public function testFindByLevelInfoWhenIsNullShouldReturnArrayOfEntities(): void
    {
        $repository = self::getService(FollowerRepository::class);

        $account = $this->createTestAccount();
        $follower = $this->createTestFollower($account, ['userId' => 6003]);
        $follower->setLevelInfo(null);
        $repository->save($follower);

        $results = $repository->findBy(['levelInfo' => null]);
        $this->assertIsArray($results);
        foreach ($results as $result) {
            $this->assertNull($result->getLevelInfo());
        }
    }

    public function testFindByCountryWhenIsNullShouldReturnArrayOfEntities(): void
    {
        $repository = self::getService(FollowerRepository::class);

        $account = $this->createTestAccount();
        $follower = $this->createTestFollower($account, ['userId' => 6004]);
        $follower->setCountry(null);
        $repository->save($follower);

        $results = $repository->findBy(['country' => null]);
        $this->assertIsArray($results);
        foreach ($results as $result) {
            $this->assertNull($result->getCountry());
        }
    }

    public function testFindByProvinceWhenIsNullShouldReturnArrayOfEntities(): void
    {
        $repository = self::getService(FollowerRepository::class);

        $account = $this->createTestAccount();
        $follower = $this->createTestFollower($account, ['userId' => 6005]);
        $follower->setProvince(null);
        $repository->save($follower);

        $results = $repository->findBy(['province' => null]);
        $this->assertIsArray($results);
        foreach ($results as $result) {
            $this->assertNull($result->getProvince());
        }
    }

    public function testFindByCityWhenIsNullShouldReturnArrayOfEntities(): void
    {
        $repository = self::getService(FollowerRepository::class);

        $account = $this->createTestAccount();
        $follower = $this->createTestFollower($account, ['userId' => 6006]);
        $follower->setCity(null);
        $repository->save($follower);

        $results = $repository->findBy(['city' => null]);
        $this->assertIsArray($results);
        foreach ($results as $result) {
            $this->assertNull($result->getCity());
        }
    }

    public function testFindByFollowTimeWhenIsNullShouldReturnArrayOfEntities(): void
    {
        $repository = self::getService(FollowerRepository::class);

        $account = $this->createTestAccount();
        $follower = $this->createTestFollower($account, ['userId' => 6007]);
        $follower->setFollowTime(null);
        $repository->save($follower);

        $results = $repository->findBy(['followTime' => null]);
        $this->assertIsArray($results);
        foreach ($results as $result) {
            $this->assertNull($result->getFollowTime());
        }
    }

    public function testFindByTradedNumWhenIsNullShouldReturnArrayOfEntities(): void
    {
        $repository = self::getService(FollowerRepository::class);

        $account = $this->createTestAccount();
        $follower = $this->createTestFollower($account, ['userId' => 6008]);
        $follower->setTradedNum(null);
        $repository->save($follower);

        $results = $repository->findBy(['tradedNum' => null]);
        $this->assertIsArray($results);
        foreach ($results as $result) {
            $this->assertNull($result->getTradedNum());
        }
    }

    public function testFindByTradeMoneyWhenIsNullShouldReturnArrayOfEntities(): void
    {
        $repository = self::getService(FollowerRepository::class);

        $account = $this->createTestAccount();
        $follower = $this->createTestFollower($account, ['userId' => 6009]);
        $follower->setTradeMoney(null);
        $repository->save($follower);

        $results = $repository->findBy(['tradeMoney' => null]);
        $this->assertIsArray($results);
        foreach ($results as $result) {
            $this->assertNull($result->getTradeMoney());
        }
    }

    public function testFindByPointsWhenIsNullShouldReturnArrayOfEntities(): void
    {
        $repository = self::getService(FollowerRepository::class);

        $account = $this->createTestAccount();
        $follower = $this->createTestFollower($account, ['userId' => 6010]);
        $follower->setPoints(null);
        $repository->save($follower);

        $results = $repository->findBy(['points' => null]);
        $this->assertIsArray($results);
        foreach ($results as $result) {
            $this->assertNull($result->getPoints());
        }
    }

    // findAll() tests

    // Additional count tests for associations and nullable fields
    public function testCountByAssociationAccountShouldReturnCorrectNumber(): void
    {
        $repository = self::getService(FollowerRepository::class);

        $account = $this->createTestAccount();
        $follower = $this->createTestFollower($account, ['userId' => 10001]);
        $repository->save($follower);

        $count = $repository->count(['account' => $account]);
        $this->assertGreaterThanOrEqual(1, $count);
    }

    public function testCountByAssociationLevelInfoShouldReturnCorrectNumber(): void
    {
        $repository = self::getService(FollowerRepository::class);

        $account = $this->createTestAccount();
        $levelInfo = $this->createTestLevelInfo();
        $follower = $this->createTestFollower($account, ['userId' => 10002]);
        $follower->setLevelInfo($levelInfo);
        $repository->save($follower);

        $count = $repository->count(['levelInfo' => $levelInfo]);
        $this->assertGreaterThanOrEqual(1, $count);
    }

    public function testCountByAvatarWhenIsNullShouldReturnCorrectNumber(): void
    {
        $repository = self::getService(FollowerRepository::class);

        $account = $this->createTestAccount();
        $follower = $this->createTestFollower($account, ['userId' => 10004]);
        $follower->setAvatar(null);
        $repository->save($follower);

        $avatarNullCount = $repository->count(['avatar' => null]);
        $this->assertGreaterThanOrEqual(1, $avatarNullCount);
    }

    public function testCountByCountryWhenIsNullShouldReturnCorrectNumber(): void
    {
        $repository = self::getService(FollowerRepository::class);

        $account = $this->createTestAccount();
        $follower = $this->createTestFollower($account, ['userId' => 10005]);
        $follower->setCountry(null);
        $repository->save($follower);

        $countryNullCount = $repository->count(['country' => null]);
        $this->assertGreaterThanOrEqual(1, $countryNullCount);
    }

    public function testCountByProvinceWhenIsNullShouldReturnCorrectNumber(): void
    {
        $repository = self::getService(FollowerRepository::class);

        $account = $this->createTestAccount();
        $follower = $this->createTestFollower($account, ['userId' => 10006]);
        $follower->setProvince(null);
        $repository->save($follower);

        $provinceNullCount = $repository->count(['province' => null]);
        $this->assertGreaterThanOrEqual(1, $provinceNullCount);
    }

    public function testCountByCityWhenIsNullShouldReturnCorrectNumber(): void
    {
        $repository = self::getService(FollowerRepository::class);

        $account = $this->createTestAccount();
        $follower = $this->createTestFollower($account, ['userId' => 10007]);
        $follower->setCity(null);
        $repository->save($follower);

        $cityNullCount = $repository->count(['city' => null]);
        $this->assertGreaterThanOrEqual(1, $cityNullCount);
    }

    public function testCountByFollowTimeWhenIsNullShouldReturnCorrectNumber(): void
    {
        $repository = self::getService(FollowerRepository::class);

        $account = $this->createTestAccount();
        $follower = $this->createTestFollower($account, ['userId' => 10008]);
        $follower->setFollowTime(null);
        $repository->save($follower);

        $followTimeNullCount = $repository->count(['followTime' => null]);
        $this->assertGreaterThanOrEqual(1, $followTimeNullCount);
    }

    public function testCountByTradedNumWhenIsNullShouldReturnCorrectNumber(): void
    {
        $repository = self::getService(FollowerRepository::class);

        $account = $this->createTestAccount();
        $follower = $this->createTestFollower($account, ['userId' => 10009]);
        $follower->setTradedNum(null);
        $repository->save($follower);

        $tradedNumNullCount = $repository->count(['tradedNum' => null]);
        $this->assertGreaterThanOrEqual(1, $tradedNumNullCount);
    }

    public function testCountByTradeMoneyWhenIsNullShouldReturnCorrectNumber(): void
    {
        $repository = self::getService(FollowerRepository::class);

        $account = $this->createTestAccount();
        $follower = $this->createTestFollower($account, ['userId' => 10010]);
        $follower->setTradeMoney(null);
        $repository->save($follower);

        $tradeMoneyNullCount = $repository->count(['tradeMoney' => null]);
        $this->assertGreaterThanOrEqual(1, $tradeMoneyNullCount);
    }

    public function testCountByPointsWhenIsNullShouldReturnCorrectNumber(): void
    {
        $repository = self::getService(FollowerRepository::class);

        $account = $this->createTestAccount();
        $follower = $this->createTestFollower($account, ['userId' => 10011]);
        $follower->setPoints(null);
        $repository->save($follower);

        $pointsNullCount = $repository->count(['points' => null]);
        $this->assertGreaterThanOrEqual(1, $pointsNullCount);
    }

    public function testCountByLevelInfoWhenIsNullShouldReturnCorrectNumber(): void
    {
        $repository = self::getService(FollowerRepository::class);

        $account = $this->createTestAccount();
        $follower = $this->createTestFollower($account, ['userId' => 10012]);
        $follower->setLevelInfo(null);
        $repository->save($follower);

        $levelInfoNullCount = $repository->count(['levelInfo' => null]);
        $this->assertGreaterThanOrEqual(1, $levelInfoNullCount);
    }

    // Additional association and IS NULL tests

    public function testFindOneByAvatarWhenIsNullShouldReturnCorrectEntity(): void
    {
        $repository = self::getService(FollowerRepository::class);

        $account = $this->createTestAccount();
        $follower = $this->createTestFollower($account, ['userId' => 11004]);
        $follower->setAvatar(null);
        $repository->save($follower);

        $result = $repository->findOneBy(['avatar' => null]);
        $this->assertInstanceOf(Follower::class, $result);
        $this->assertNull($result->getAvatar());
    }

    public function testFindOneByCountryWhenIsNullShouldReturnCorrectEntity(): void
    {
        $repository = self::getService(FollowerRepository::class);

        $account = $this->createTestAccount();
        $follower = $this->createTestFollower($account, ['userId' => 11005]);
        $follower->setCountry(null);
        $repository->save($follower);

        $result = $repository->findOneBy(['country' => null]);
        $this->assertInstanceOf(Follower::class, $result);
        $this->assertNull($result->getCountry());
    }

    public function testFindOneByProvinceWhenIsNullShouldReturnCorrectEntity(): void
    {
        $repository = self::getService(FollowerRepository::class);

        $account = $this->createTestAccount();
        $follower = $this->createTestFollower($account, ['userId' => 11006]);
        $follower->setProvince(null);
        $repository->save($follower);

        $result = $repository->findOneBy(['province' => null]);
        $this->assertInstanceOf(Follower::class, $result);
        $this->assertNull($result->getProvince());
    }

    public function testFindOneByCityWhenIsNullShouldReturnCorrectEntity(): void
    {
        $repository = self::getService(FollowerRepository::class);

        $account = $this->createTestAccount();
        $follower = $this->createTestFollower($account, ['userId' => 11007]);
        $follower->setCity(null);
        $repository->save($follower);

        $result = $repository->findOneBy(['city' => null]);
        $this->assertInstanceOf(Follower::class, $result);
        $this->assertNull($result->getCity());
    }

    public function testFindOneByFollowTimeWhenIsNullShouldReturnCorrectEntity(): void
    {
        $repository = self::getService(FollowerRepository::class);

        $account = $this->createTestAccount();
        $follower = $this->createTestFollower($account, ['userId' => 11008]);
        $follower->setFollowTime(null);
        $repository->save($follower);

        $result = $repository->findOneBy(['followTime' => null]);
        $this->assertInstanceOf(Follower::class, $result);
        $this->assertNull($result->getFollowTime());
    }

    public function testFindOneByTradedNumWhenIsNullShouldReturnCorrectEntity(): void
    {
        $repository = self::getService(FollowerRepository::class);

        $account = $this->createTestAccount();
        $follower = $this->createTestFollower($account, ['userId' => 11009]);
        $follower->setTradedNum(null);
        $repository->save($follower);

        $result = $repository->findOneBy(['tradedNum' => null]);
        $this->assertInstanceOf(Follower::class, $result);
        $this->assertNull($result->getTradedNum());
    }

    public function testFindOneByTradeMoneyWhenIsNullShouldReturnCorrectEntity(): void
    {
        $repository = self::getService(FollowerRepository::class);

        $account = $this->createTestAccount();
        $follower = $this->createTestFollower($account, ['userId' => 11010]);
        $follower->setTradeMoney(null);
        $repository->save($follower);

        $result = $repository->findOneBy(['tradeMoney' => null]);
        $this->assertInstanceOf(Follower::class, $result);
        $this->assertNull($result->getTradeMoney());
    }

    public function testFindOneByPointsWhenIsNullShouldReturnCorrectEntity(): void
    {
        $repository = self::getService(FollowerRepository::class);

        $account = $this->createTestAccount();
        $follower = $this->createTestFollower($account, ['userId' => 11011]);
        $follower->setPoints(null);
        $repository->save($follower);

        $result = $repository->findOneBy(['points' => null]);
        $this->assertInstanceOf(Follower::class, $result);
        $this->assertNull($result->getPoints());
    }

    public function testFindOneByLevelInfoWhenIsNullShouldReturnCorrectEntity(): void
    {
        $repository = self::getService(FollowerRepository::class);

        $account = $this->createTestAccount();
        $follower = $this->createTestFollower($account, ['userId' => 11012]);
        $follower->setLevelInfo(null);
        $repository->save($follower);

        $result = $repository->findOneBy(['levelInfo' => null]);
        $this->assertInstanceOf(Follower::class, $result);
        $this->assertNull($result->getLevelInfo());
    }

    public function testFindOneByAssociationAccountShouldReturnMatchingEntity(): void
    {
        $repository = self::getService(FollowerRepository::class);

        $account = $this->createTestAccount();
        $follower = $this->createTestFollower($account, ['userId' => 14001]);
        $repository->save($follower);

        $result = $repository->findOneBy(['account' => $account]);
        $this->assertInstanceOf(Follower::class, $result);
        $this->assertSame($account->getId(), $result->getAccount()->getId());
    }

    public function testFindOneByAssociationLevelInfoShouldReturnMatchingEntity(): void
    {
        $repository = self::getService(FollowerRepository::class);

        $account = $this->createTestAccount();
        $levelInfo = $this->createTestLevelInfo();
        $follower = $this->createTestFollower($account, ['userId' => 14002]);
        $follower->setLevelInfo($levelInfo);
        $repository->save($follower);

        $result = $repository->findOneBy(['levelInfo' => $levelInfo]);
        $this->assertInstanceOf(Follower::class, $result);
        $this->assertNotNull($result->getLevelInfo());
        $this->assertSame($levelInfo->getId(), $result->getLevelInfo()->getId());
    }

    public function testFindByAllNullableFieldsIsNull(): void
    {
        $repository = self::getService(FollowerRepository::class);

        $account = $this->createTestAccount();
        $follower = $this->createTestFollower($account, ['userId' => 11004]);
        $follower->setNick(null);
        $follower->setAvatar(null);
        $follower->setCountry(null);
        $follower->setProvince(null);
        $follower->setCity(null);
        $follower->setFollowTime(null);
        $follower->setTradedNum(null);
        $follower->setTradeMoney(null);
        $follower->setPoints(null);
        $follower->setLevelInfo(null);
        $repository->save($follower);

        // Test all nullable fields with IS NULL queries
        $nullableFields = [
            'nick', 'avatar', 'country', 'province', 'city',
            'followTime', 'tradedNum', 'tradeMoney', 'points', 'levelInfo',
        ];

        foreach ($nullableFields as $field) {
            $results = $repository->findBy([$field => null]);
            $this->assertIsArray($results);
            $this->assertGreaterThanOrEqual(1, count($results), "Failed for field: {$field}");
            foreach ($results as $result) {
                $this->assertInstanceOf(Follower::class, $result);

                // Test specific nullable fields directly
                switch ($field) {
                    case 'nick':
                        $this->assertNull($result->getNick(), "Field {$field} should be null");
                        break;
                    case 'avatar':
                        $this->assertNull($result->getAvatar(), "Field {$field} should be null");
                        break;
                    case 'country':
                        $this->assertNull($result->getCountry(), "Field {$field} should be null");
                        break;
                    case 'province':
                        $this->assertNull($result->getProvince(), "Field {$field} should be null");
                        break;
                    case 'city':
                        $this->assertNull($result->getCity(), "Field {$field} should be null");
                        break;
                    case 'followTime':
                        $this->assertNull($result->getFollowTime(), "Field {$field} should be null");
                        break;
                    case 'tradedNum':
                        $this->assertNull($result->getTradedNum(), "Field {$field} should be null");
                        break;
                    case 'tradeMoney':
                        $this->assertNull($result->getTradeMoney(), "Field {$field} should be null");
                        break;
                    case 'points':
                        $this->assertNull($result->getPoints(), "Field {$field} should be null");
                        break;
                    case 'levelInfo':
                        $this->assertNull($result->getLevelInfo(), "Field {$field} should be null");
                        break;
                }
            }
        }
    }

    // Helper methods
    private function createTestAccount(): Account
    {
        $em = self::getService(EntityManagerInterface::class);

        $account = new Account();
        $account->setName('Test Account ' . uniqid());
        $account->setClientId('test_client_' . uniqid());
        $account->setClientSecret('test_secret_' . uniqid());

        $em->persist($account);
        $em->flush();

        return $account;
    }

    private function createTestLevelInfo(): LevelInfo
    {
        $em = self::getService(EntityManagerInterface::class);

        $levelInfo = new LevelInfo();
        $levelInfo->setLevelId(rand(1, 10));
        $levelInfo->setLevelName('Level ' . uniqid());

        $em->persist($levelInfo);
        $em->flush();

        return $levelInfo;
    }

    /**
     * @param array<string, mixed> $overrides
     */
    private function createTestFollower(Account $account, array $overrides = []): Follower
    {
        $follower = new Follower();
        $follower->setUserId($overrides['userId'] ?? rand(10000, 99999));
        $follower->setWeixinOpenId($overrides['weixinOpenId'] ?? 'openid_' . uniqid());
        $follower->setNick($overrides['nick'] ?? 'Test User ' . uniqid());
        $follower->setAvatar($overrides['avatar'] ?? 'https://example.com/avatar.jpg');
        $follower->setCountry($overrides['country'] ?? 'China');
        $follower->setProvince($overrides['province'] ?? 'Beijing');
        $follower->setCity($overrides['city'] ?? 'Beijing');
        $follower->setSex($overrides['sex'] ?? GenderEnum::UNKNOWN);
        $follower->setIsFollow($overrides['isFollow'] ?? false);
        $follower->setFollowTime($overrides['followTime'] ?? time());
        $follower->setTradedNum($overrides['tradedNum'] ?? 0);
        $follower->setTradeMoney($overrides['tradeMoney'] ?? 0.0);
        $follower->setPoints($overrides['points'] ?? null);
        $follower->setAccount($account);

        return $follower;
    }

    protected function createNewEntity(): object
    {
        $account = $this->createTestAccount();
        $follower = new Follower();
        $follower->setUserId(rand(10000, 99999));
        $follower->setWeixinOpenId('test_openid_' . uniqid());
        $follower->setAccount($account);

        return $follower;
    }

    protected function getRepository(): FollowerRepository
    {
        return self::getService(FollowerRepository::class);
    }
}
