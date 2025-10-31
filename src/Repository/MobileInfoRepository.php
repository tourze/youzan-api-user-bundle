<?php

namespace YouzanApiUserBundle\Repository;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Tourze\PHPUnitSymfonyKernelTest\Attribute\AsRepository;
use YouzanApiUserBundle\Entity\MobileInfo;

/**
 * 有赞用户手机信息仓库类
 * @extends ServiceEntityRepository<MobileInfo>
 */
#[AsRepository(entityClass: MobileInfo::class)]
class MobileInfoRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, MobileInfo::class);
    }

    /**
     * 根据手机号查询用户手机信息
     * @return MobileInfo|null
     */
    public function findByMobileDecrypted(string $mobile): ?MobileInfo
    {
        return $this->findOneBy(['mobileDecrypted' => $mobile]);
    }

    /**
     * 根据加密手机号查询用户手机信息
     * @return MobileInfo|null
     */
    public function findByMobileEncrypted(string $mobile): ?MobileInfo
    {
        return $this->findOneBy(['mobileEncrypted' => $mobile]);
    }

    public function save(MobileInfo $entity, bool $flush = true): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(MobileInfo $entity, bool $flush = true): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }
}
