<?php

namespace YouzanApiUserBundle\Repository;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use YouzanApiUserBundle\Entity\LevelInfo;

/**
 * 有赞用户等级信息仓库类
 *
 * @method LevelInfo|null find($id, $lockMode = null, $lockVersion = null)
 * @method LevelInfo|null findOneBy(array $criteria, array $orderBy = null)
 * @method LevelInfo[] findAll()
 * @method LevelInfo[] findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class LevelInfoRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, LevelInfo::class);
    }

    /**
     * 根据等级ID查询用户等级信息
     */
    public function findByLevelId(int $levelId): array
    {
        return $this->findBy(['levelId' => $levelId]);
    }

    /**
     * 根据等级名称查询用户等级信息
     */
    public function findByLevelName(string $levelName): array
    {
        return $this->findBy(['levelName' => $levelName]);
    }
}
