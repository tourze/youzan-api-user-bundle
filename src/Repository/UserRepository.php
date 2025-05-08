<?php

namespace YouzanApiUserBundle\Repository;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use YouzanApiUserBundle\Entity\User;

/**
 * 有赞用户仓库类
 *
 * @method User|null find($id, $lockMode = null, $lockVersion = null)
 * @method User|null findOneBy(array $criteria, array $orderBy = null)
 * @method User[] findAll()
 * @method User[] findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class UserRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, User::class);
    }

    /**
     * 根据有赞 OpenID 查询用户
     */
    public function findByYzOpenId(string $yzOpenId): ?User
    {
        return $this->findOneBy(['yzOpenId' => $yzOpenId]);
    }

    /**
     * 根据微信 UnionID 查询用户
     */
    public function findByUnionId(string $unionId): ?User
    {
        return $this->findOneBy(['unionId' => $unionId]);
    }

    /**
     * 根据微信 OpenID 查询用户
     */
    public function findByWeixinOpenId(string $weixinOpenId): ?User
    {
        return $this->findOneBy(['weixinOpenId' => $weixinOpenId]);
    }

    /**
     * 根据手机号查询用户
     */
    public function findByMobile(string $mobile, string $countryCode = '+86'): ?User
    {
        return $this->findOneBy([
            'mobile' => $mobile,
            'countryCode' => $countryCode
        ]);
    }

    /**
     * 根据关注时间段查询用户列表
     */
    public function findByFollowTimeRange(\DateTime $startTime, \DateTime $endTime)
    {
        return $this->createQueryBuilder('u')
            ->where('u.followTime >= :startTime')
            ->andWhere('u.followTime <= :endTime')
            ->setParameter('startTime', $startTime)
            ->setParameter('endTime', $endTime)
            ->getQuery()
            ->getResult();
    }

    /**
     * 查询所有粉丝用户
     */
    public function findAllFans()
    {
        return $this->findBy(['isFans' => true]);
    }

    /**
     * 根据平台类型查询用户
     */
    public function findByPlatformType(int $platformType)
    {
        return $this->findBy(['platformType' => $platformType]);
    }
}
