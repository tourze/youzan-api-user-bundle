<?php

namespace YouzanApiUserBundle\Repository;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use YouzanApiUserBundle\Entity\WechatInfo;
use YouzanApiUserBundle\Enum\FansStatusEnum;

/**
 * 有赞用户微信信息仓库类
 *
 * @method WechatInfo|null find($id, $lockMode = null, $lockVersion = null)
 * @method WechatInfo|null findOneBy(array $criteria, array $orderBy = null)
 * @method WechatInfo[] findAll()
 * @method WechatInfo[] findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class WechatInfoRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, WechatInfo::class);
    }

    /**
     * 根据微信 UnionID 查询微信信息
     */
    public function findByUnionId(string $unionId): ?WechatInfo
    {
        return $this->findOneBy(['unionId' => $unionId]);
    }

    /**
     * 查询所有粉丝
     */
    public function findAllFans(): array
    {
        return $this->findBy(['fansStatus' => FansStatusEnum::FOLLOWED]);
    }
}
