<?php

namespace YouzanApiUserBundle\Tests\Repository;

use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Mapping\ClassMetadata;
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

        $classMetadata = $this->createMock(ClassMetadata::class);
        $classMetadata->name = User::class;

        $this->registry->expects($this->any())
            ->method('getManagerForClass')
            ->willReturn($this->entityManager);

        $this->entityManager->expects($this->any())
            ->method('getClassMetadata')
            ->willReturn($classMetadata);

        $this->repository = new UserRepository($this->registry);
    }

    public function testCanBeInstantiated(): void
    {
        $this->assertInstanceOf(UserRepository::class, $this->repository);
    }

    public function testGetEntityClass(): void
    {
        $this->assertSame(User::class, $this->repository->getClassName());
    }
}
