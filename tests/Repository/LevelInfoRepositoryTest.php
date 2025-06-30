<?php

namespace YouzanApiUserBundle\Tests\Repository;

use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ManagerRegistry;
use PHPUnit\Framework\TestCase;
use YouzanApiUserBundle\Repository\LevelInfoRepository;

class LevelInfoRepositoryTest extends TestCase
{
    private EntityManagerInterface $entityManager;
    private ManagerRegistry $registry;

    protected function setUp(): void
    {
        $this->entityManager = $this->createMock(EntityManagerInterface::class);
        $this->registry = $this->createMock(ManagerRegistry::class);

        $this->registry->expects($this->any())
            ->method('getManagerForClass')
            ->willReturn($this->entityManager);
    }

    public function testCanBeInstantiated(): void
    {
        $repository = new LevelInfoRepository($this->registry);
        $this->assertInstanceOf(LevelInfoRepository::class, $repository);
    }
}