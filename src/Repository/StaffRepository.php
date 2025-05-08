<?php

namespace YouzanApiUserBundle\Repository;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use YouzanApiUserBundle\Entity\Staff;

/**
 * 有赞员工仓库类
 *
 * @method Staff|null find($id, $lockMode = null, $lockVersion = null)
 * @method Staff|null findOneBy(array $criteria, array $orderBy = null)
 * @method Staff[] findAll()
 * @method Staff[] findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class StaffRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Staff::class);
    }

    /**
     * 根据企业ID查询员工列表
     */
    public function findByCorpId(string $corpId): array
    {
        return $this->findBy(['corpId' => $corpId]);
    }

    /**
     * 根据店铺ID查询员工列表
     */
    public function findByKdtId(int $kdtId): array
    {
        return $this->findBy(['kdtId' => $kdtId]);
    }

    /**
     * 根据邮箱查询员工
     */
    public function findByEmail(string $email): ?Staff
    {
        return $this->findOneBy(['email' => $email]);
    }
}
