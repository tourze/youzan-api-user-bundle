<?php

namespace YouzanApiUserBundle\Entity;

use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Tourze\DoctrineTimestampBundle\Traits\TimestampableAware;
use YouzanApiUserBundle\Enum\FansStatusEnum;
use YouzanApiUserBundle\Enum\WechatTypeEnum;
use YouzanApiUserBundle\Repository\WechatInfoRepository;

/**
 * 有赞用户微信信息实体
 */
#[ORM\Entity(repositoryClass: WechatInfoRepository::class)]
#[ORM\Table(name: 'ims_youzan_user_wechat_info', options: ['comment' => '有赞用户微信信息表'])]
class WechatInfo implements \Stringable
{
    use TimestampableAware;

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: Types::INTEGER, options: ['comment' => 'ID'])]
    private int $id = 0;

    public function getId(): int
    {
        return $this->id;
    }

    #[ORM\Column(type: Types::INTEGER, nullable: true, enumType: WechatTypeEnum::class, options: ['comment' => '微信类型'])]
    #[Assert\Choice(callback: [WechatTypeEnum::class, 'cases'])]
    private ?WechatTypeEnum $wechatType = null;

    #[ORM\Column(type: Types::INTEGER, enumType: FansStatusEnum::class, options: ['comment' => '粉丝状态'])]
    #[Assert\Choice(callback: [FansStatusEnum::class, 'cases'])]
    private FansStatusEnum $fansStatus = FansStatusEnum::UNFOLLOWED;

    #[ORM\Column(type: Types::DATETIME_IMMUTABLE, nullable: true, options: ['comment' => '关注时间'])]
    #[Assert\Type(type: \DateTimeImmutable::class, message: 'Follow time must be a DateTimeImmutable instance')]
    private ?\DateTimeImmutable $followTime = null;

    #[ORM\Column(type: Types::DATETIME_IMMUTABLE, nullable: true, options: ['comment' => '最后交谈时间'])]
    #[Assert\Type(type: \DateTimeImmutable::class, message: 'Last talk time must be a DateTimeImmutable instance')]
    private ?\DateTimeImmutable $lastTalkTime = null;

    #[ORM\Column(type: Types::STRING, length: 64, nullable: true, options: ['comment' => '微信 UnionID'])]
    #[Assert\Length(max: 64)]
    private ?string $unionId = null;

    #[ORM\Column(type: Types::DATETIME_IMMUTABLE, nullable: true, options: ['comment' => '取消关注时间'])]
    #[Assert\Type(type: \DateTimeImmutable::class, message: 'Unfollow time must be a DateTimeImmutable instance')]
    private ?\DateTimeImmutable $unfollowTime = null;

    /**
     * 关联的有赞用户（单向关联）
     */
    #[ORM\OneToOne(targetEntity: User::class, cascade: ['persist'])]
    #[ORM\JoinColumn(name: 'user_id', referencedColumnName: 'id', nullable: false)]
    private User $user;

    public function getWechatType(): ?WechatTypeEnum
    {
        return $this->wechatType;
    }

    public function setWechatType(?WechatTypeEnum $wechatType): void
    {
        $this->wechatType = $wechatType;
    }

    public function getFansStatus(): FansStatusEnum
    {
        return $this->fansStatus;
    }

    public function setFansStatus(FansStatusEnum $fansStatus): void
    {
        $this->fansStatus = $fansStatus;
    }

    public function getFollowTime(): ?\DateTimeImmutable
    {
        return $this->followTime;
    }

    public function setFollowTime(?\DateTimeImmutable $followTime): void
    {
        $this->followTime = $followTime;
    }

    public function getLastTalkTime(): ?\DateTimeImmutable
    {
        return $this->lastTalkTime;
    }

    public function setLastTalkTime(?\DateTimeImmutable $lastTalkTime): void
    {
        $this->lastTalkTime = $lastTalkTime;
    }

    public function getUnionId(): ?string
    {
        return $this->unionId;
    }

    public function setUnionId(?string $unionId): void
    {
        $this->unionId = $unionId;
    }

    public function getUnfollowTime(): ?\DateTimeImmutable
    {
        return $this->unfollowTime;
    }

    public function setUnfollowTime(?\DateTimeImmutable $unfollowTime): void
    {
        $this->unfollowTime = $unfollowTime;
    }

    public function getUser(): User
    {
        return $this->user;
    }

    public function setUser(User $user): void
    {
        $this->user = $user;
    }

    /**
     * 判断是否为粉丝（已关注）
     */
    public function isFans(): bool
    {
        return FansStatusEnum::FOLLOWED === $this->fansStatus;
    }

    public function __toString(): string
    {
        return 0 !== $this->getId()
            ? "{$this->getUser()->getNickNameDecrypted()}[{$this->getUnionId()}]"
            : '';
    }
}
