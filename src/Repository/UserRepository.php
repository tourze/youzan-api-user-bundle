<?php

namespace YouzanApiUserBundle\Repository;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Tourze\PHPUnitSymfonyKernelTest\Attribute\AsRepository;
use YouzanApiUserBundle\Entity\User;

/**
 * 有赞用户仓库类
 * @extends ServiceEntityRepository<User>
 */
#[AsRepository(entityClass: User::class)]
class UserRepository extends ServiceEntityRepository
{
    public function __construct(
        ManagerRegistry $registry,
        private readonly WechatInfoRepository $wechatInfoRepository,
        private readonly MobileInfoRepository $mobileInfoRepository,
    ) {
        parent::__construct($registry, User::class);
    }

    /**
     * 根据有赞 OpenID 查询用户
     * @return User|null
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
        // 通过 WechatInfo 实体查询，因为关联现在是单向的
        $wechatInfo = $this->wechatInfoRepository->findByUnionId($unionId);

        return null !== $wechatInfo ? $wechatInfo->getUser() : null;
    }

    /**
     * 根据微信 OpenID 查询用户
     * 注意：当前 WechatInfo 实体中没有 openId 字段，使用 unionId 代替
     */
    public function findByWeixinOpenId(string $weixinOpenId): ?User
    {
        // 通过 WechatInfo 实体查询，因为关联现在是单向的
        $wechatInfo = $this->wechatInfoRepository->findByUnionId($weixinOpenId);

        return null !== $wechatInfo ? $wechatInfo->getUser() : null;
    }

    /**
     * 根据手机号查询用户
     */
    public function findByMobile(string $mobile, string $countryCode = '+86'): ?User
    {
        // 通过 MobileInfo 实体查询，因为关联现在是单向的
        $mobileInfo = $this->mobileInfoRepository->findByMobileDecrypted($mobile);

        return null !== $mobileInfo ? $mobileInfo->getUser() : null;
    }

    /**
     * 根据关注时间段查询用户列表
     * @return array<User>
     */
    public function findByFollowTimeRange(\DateTime $startTime, \DateTime $endTime): array
    {
        // 通过 WechatInfo 实体查询，因为关联现在是单向的
        $wechatInfos = $this->wechatInfoRepository->createQueryBuilder('w')
            ->where('w.followTime >= :startTime')
            ->andWhere('w.followTime <= :endTime')
            ->setParameter('startTime', \DateTimeImmutable::createFromMutable($startTime))
            ->setParameter('endTime', \DateTimeImmutable::createFromMutable($endTime))
            ->getQuery()
            ->getResult()
        ;

        // 提取用户
        $users = [];
        foreach ($wechatInfos as $wechatInfo) {
            $users[] = $wechatInfo->getUser();
        }

        return $users;
    }

    /**
     * 查询所有粉丝用户
     * @return array<User>
     */
    public function findAllFans(): array
    {
        // 通过 WechatInfo 实体查询，因为关联现在是单向的
        $wechatInfos = $this->wechatInfoRepository->findAllFans();

        // 提取用户
        $users = [];
        foreach ($wechatInfos as $wechatInfo) {
            $users[] = $wechatInfo->getUser();
        }

        return $users;
    }

    /**
     * 根据平台类型查询用户
     * @return array<User>
     */
    public function findByPlatformType(int $platformType): array
    {
        return $this->findBy(['platformType' => $platformType]);
    }

    public function save(User $entity, bool $flush = true): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(User $entity, bool $flush = true): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }
}
