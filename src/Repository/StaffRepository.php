<?php

namespace YouzanApiUserBundle\Repository;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Tourze\PHPUnitSymfonyKernelTest\Attribute\AsRepository;
use YouzanApiUserBundle\Entity\Staff;

/**
 * 有赞员工仓库类
 *
 * @extends ServiceEntityRepository<Staff>
 */
#[AsRepository(entityClass: Staff::class)]
class StaffRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Staff::class);
    }

    /**
     * 根据企业ID查询员工列表
     *
     * @return array<Staff>
     */
    public function findByCorpId(string $corpId): array
    {
        return $this->findBy(['corpId' => $corpId]);
    }

    /**
     * 根据店铺ID查询员工列表
     *
     * @return array<Staff>
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

    public function save(Staff $entity, bool $flush = true): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(Staff $entity, bool $flush = true): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }
}
