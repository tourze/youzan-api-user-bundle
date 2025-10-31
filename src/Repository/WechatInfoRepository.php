<?php

namespace YouzanApiUserBundle\Repository;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Tourze\PHPUnitSymfonyKernelTest\Attribute\AsRepository;
use YouzanApiUserBundle\Entity\WechatInfo;
use YouzanApiUserBundle\Enum\FansStatusEnum;

/**
 * 有赞用户微信信息仓库类
 * @extends ServiceEntityRepository<WechatInfo>
 */
#[AsRepository(entityClass: WechatInfo::class)]
class WechatInfoRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, WechatInfo::class);
    }

    /**
     * 根据微信 UnionID 查询微信信息
     * @return WechatInfo|null
     */
    public function findByUnionId(string $unionId): ?WechatInfo
    {
        return $this->findOneBy(['unionId' => $unionId]);
    }

    /**
     * 查询所有粉丝
     * @return array<WechatInfo>
     */
    public function findAllFans(): array
    {
        return $this->findBy(['fansStatus' => FansStatusEnum::FOLLOWED]);
    }

    public function save(WechatInfo $entity, bool $flush = true): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(WechatInfo $entity, bool $flush = true): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }
}
