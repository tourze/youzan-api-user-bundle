<?php

namespace YouzanApiUserBundle\Repository;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use YouzanApiUserBundle\Entity\Follower;
use YouzanApiUserBundle\Enum\FansStatusEnum;

/**
 * 有赞粉丝仓库类
 *
 * @method Follower|null find($id, $lockMode = null, $lockVersion = null)
 * @method Follower|null findOneBy(array $criteria, array $orderBy = null)
 * @method Follower[] findAll()
 * @method Follower[] findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class FollowerRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Follower::class);
    }

    /**
     * 根据有赞用户ID查询粉丝
     */
    public function findByUserId(int $userId): ?Follower
    {
        return $this->findOneBy(['userId' => $userId]);
    }

    /**
     * 根据微信 OpenID 查询粉丝
     */
    public function findByWeixinOpenId(string $weixinOpenId): ?Follower
    {
        return $this->findOneBy(['weixinOpenId' => $weixinOpenId]);
    }

    /**
     * 查询所有关注的粉丝
     */
    public function findAllFollowed(): array
    {
        return $this->findBy(['fansStatus' => FansStatusEnum::FOLLOWED]);
    }

    /**
     * 根据昵称模糊查询粉丝
     */
    public function findByNickLike(string $nick): array
    {
        $qb = $this->createQueryBuilder('f');
        return $qb->where($qb->expr()->like('f.nick', ':nick'))
            ->setParameter('nick', '%' . $nick . '%')
            ->getQuery()
            ->getResult();
    }
}
