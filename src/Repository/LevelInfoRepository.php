<?php

namespace YouzanApiUserBundle\Repository;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Tourze\PHPUnitSymfonyKernelTest\Attribute\AsRepository;
use YouzanApiUserBundle\Entity\LevelInfo;

/**
 * 有赞用户等级信息仓库类
 * @extends ServiceEntityRepository<LevelInfo>
 */
#[AsRepository(entityClass: LevelInfo::class)]
class LevelInfoRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, LevelInfo::class);
    }

    /**
     * 根据等级ID查询用户等级信息
     * @return array<LevelInfo>
     */
    public function findByLevelId(int $levelId): array
    {
        return $this->findBy(['levelId' => $levelId]);
    }

    /**
     * 根据等级名称查询用户等级信息
     * @return array<LevelInfo>
     */
    public function findByLevelName(string $levelName): array
    {
        return $this->findBy(['levelName' => $levelName]);
    }

    public function save(LevelInfo $entity, bool $flush = true): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(LevelInfo $entity, bool $flush = true): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }
}
