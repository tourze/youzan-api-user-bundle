<?php

namespace YouzanApiUserBundle\Tests\Repository;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\RunTestsInSeparateProcesses;
use PHPUnit\Framework\Attributes\TestWith;
use Tourze\PHPUnitSymfonyKernelTest\AbstractRepositoryTestCase;
use YouzanApiUserBundle\Entity\LevelInfo;
use YouzanApiUserBundle\Repository\LevelInfoRepository;

/**
 * @internal
 */
#[CoversClass(LevelInfoRepository::class)]
#[RunTestsInSeparateProcesses]
final class LevelInfoRepositoryTest extends AbstractRepositoryTestCase
{
    private LevelInfoRepository $repository;

    protected function onSetUp(): void
    {
        $this->repository = self::getService(LevelInfoRepository::class);
    }

    public function testCanBeInstantiated(): void
    {
        $repository = self::getService(LevelInfoRepository::class);

        $this->assertInstanceOf(LevelInfoRepository::class, $repository);
        $this->assertSame(LevelInfo::class, $repository->getClassName());
    }

    // Tests for find() method

    // Tests for findAll() method

    // Tests for findBy() method

    // Tests for findOneBy() method

    public function testFindOneByWithMultipleMatchesShouldReturnFirstEntity(): void
    {
        $entity1 = new LevelInfo();
        $entity1->setLevelId(1200);
        $entity1->setLevelName('Duplicate A');
        $entity2 = new LevelInfo();
        $entity2->setLevelId(1200);
        $entity2->setLevelName('Duplicate B');

        $this->repository->save($entity1);
        $this->repository->save($entity2);

        $result = $this->repository->findOneBy(['levelId' => 1200]);
        $this->assertNotNull($result);
        $this->assertInstanceOf(LevelInfo::class, $result);
        $this->assertSame(1200, $result->getLevelId());
    }

    #[TestWith(['nonExistentField'])]
    #[TestWith(['anotherInvalidField'])]
    #[TestWith(['_non_existent_field_with_underscores'])]
    // Tests for custom methods
    public function testFindByLevelId(): void
    {
        $entity = new LevelInfo();
        $entity->setLevelId(2000);
        $entity->setLevelName('Custom Level');
        $this->repository->save($entity);

        $result = $this->repository->findByLevelId(2000);
        $this->assertIsArray($result);
        $this->assertCount(1, $result);
        $this->assertInstanceOf(LevelInfo::class, $result[0]);
        $this->assertSame(2000, $result[0]->getLevelId());
    }

    public function testFindByLevelName(): void
    {
        $entity = new LevelInfo();
        $entity->setLevelId(2100);
        $entity->setLevelName('Named Level');
        $this->repository->save($entity);

        $result = $this->repository->findByLevelName('Named Level');
        $this->assertIsArray($result);
        $this->assertCount(1, $result);
        $this->assertInstanceOf(LevelInfo::class, $result[0]);
        $this->assertSame('Named Level', $result[0]->getLevelName());
    }

    public function testSaveWithoutFlushShouldNotPersistImmediately(): void
    {
        $entity = new LevelInfo();
        $entity->setLevelId(2300);
        $entity->setLevelName('No Flush Test');

        $this->repository->save($entity, false);
        $this->assertNotNull($entity->getId());

        self::getEntityManager()->clear();
        $found = $this->repository->find($entity->getId());
        $this->assertNull($found);
    }

    // Tests for remove() method
    public function testRemoveWithFlushShouldDeleteEntity(): void
    {
        $entity = new LevelInfo();
        $entity->setLevelId(2400);
        $entity->setLevelName('Remove Test');
        $this->repository->save($entity);
        $savedId = $entity->getId();

        $this->repository->remove($entity, true);
        $found = $this->repository->find($savedId);
        $this->assertNull($found);
    }

    public function testRemoveWithoutFlushShouldNotDeleteImmediately(): void
    {
        $entity = new LevelInfo();
        $entity->setLevelId(2500);
        $entity->setLevelName('No Flush Remove Test');
        $this->repository->save($entity);
        $savedId = $entity->getId();

        $this->repository->remove($entity, false);
        self::getEntityManager()->clear();
        $found = $this->repository->find($savedId);
        $this->assertNotNull($found);
    }

    protected function createNewEntity(): object
    {
        $levelInfo = new LevelInfo();
        $levelInfo->setLevelId(rand(1, 100));
        $levelInfo->setLevelName('Test Level ' . uniqid());

        return $levelInfo;
    }

    protected function getRepository(): LevelInfoRepository
    {
        return $this->repository;
    }
}
