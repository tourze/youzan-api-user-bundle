<?php

namespace YouzanApiUserBundle\Repository;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use YouzanApiUserBundle\Entity\MobileInfo;

/**
 * 有赞用户手机信息仓库类
 *
 * @method MobileInfo|null find($id, $lockMode = null, $lockVersion = null)
 * @method MobileInfo|null findOneBy(array $criteria, array $orderBy = null)
 * @method MobileInfo[] findAll()
 * @method MobileInfo[] findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class MobileInfoRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, MobileInfo::class);
    }

    /**
     * 根据手机号查询用户手机信息
     */
    public function findByMobileDecrypted(string $mobile): ?MobileInfo
    {
        return $this->findOneBy(['mobileDecrypted' => $mobile]);
    }

    /**
     * 根据加密手机号查询用户手机信息
     */
    public function findByMobileEncrypted(string $mobile): ?MobileInfo
    {
        return $this->findOneBy(['mobileEncrypted' => $mobile]);
    }
}
