<?php

namespace YouzanApiUserBundle\Repository;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Tourze\PHPUnitSymfonyKernelTest\Attribute\AsRepository;
use YouzanApiUserBundle\Entity\Follower;

/**
 * 有赞粉丝仓库类
 * @extends ServiceEntityRepository<Follower>
 */
#[AsRepository(entityClass: Follower::class)]
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
     * @return array<Follower>
     */
    public function findAllFollowed(): array
    {
        return $this->findBy(['isFollow' => true]);
    }

    /**
     * 根据昵称模糊查询粉丝
     * @return array<Follower>
     */
    public function findByNickLike(string $nick): array
    {
        $qb = $this->createQueryBuilder('f');

        return $qb->where($qb->expr()->like('f.nick', ':nick'))
            ->setParameter('nick', '%' . $nick . '%')
            ->getQuery()
            ->getResult()
        ;
    }

    public function save(Follower $entity, bool $flush = true): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(Follower $entity, bool $flush = true): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }
}
