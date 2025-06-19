<?php

namespace YouzanApiUserBundle\Entity;

use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
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
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: Types::INTEGER, options: ['comment' => 'ID'])]
    private ?int $id = 0;

    public function getId(): ?int
    {
        return $this->id;
    }

    #[ORM\Column(type: Types::INTEGER, nullable: true, enumType: WechatTypeEnum::class, options: ['comment' => '微信类型'])]
    private ?WechatTypeEnum $wechatType = null;

    #[ORM\Column(type: Types::INTEGER, enumType: FansStatusEnum::class, options: ['comment' => '粉丝状态'])]
    private FansStatusEnum $fansStatus = FansStatusEnum::UNFOLLOWED;

    #[ORM\Column(type: Types::DATETIME_IMMUTABLE, nullable: true, options: ['comment' => '关注时间'])]
    private ?\DateTimeImmutable $followTime = null;

    #[ORM\Column(type: Types::DATETIME_IMMUTABLE, nullable: true, options: ['comment' => '最后交谈时间'])]
    private ?\DateTimeImmutable $lastTalkTime = null;

    #[ORM\Column(type: Types::STRING, length: 64, nullable: true, options: ['comment' => '微信 UnionID'])]
    private ?string $unionId = null;

    #[ORM\Column(type: Types::DATETIME_IMMUTABLE, nullable: true, options: ['comment' => '取消关注时间'])]
    private ?\DateTimeImmutable $unfollowTime = null;

    /**
     * 关联的有赞用户
     */
    #[ORM\OneToOne(targetEntity: User::class, inversedBy: 'wechatInfo')]
    #[ORM\JoinColumn(name: 'user_id', referencedColumnName: 'id', nullable: false)]
    private User $user;

    use TimestampableAware;

    public function getWechatType(): ?WechatTypeEnum
    {
        return $this->wechatType;
    }

    public function setWechatType(?WechatTypeEnum $wechatType): self
    {
        $this->wechatType = $wechatType;
        return $this;
    }

    public function getFansStatus(): FansStatusEnum
    {
        return $this->fansStatus;
    }

    public function setFansStatus(FansStatusEnum $fansStatus): self
    {
        $this->fansStatus = $fansStatus;
        return $this;
    }

    public function getFollowTime(): ?\DateTimeImmutable
    {
        return $this->followTime;
    }

    public function setFollowTime(?\DateTimeImmutable $followTime): self
    {
        $this->followTime = $followTime;
        return $this;
    }

    public function getLastTalkTime(): ?\DateTimeImmutable
    {
        return $this->lastTalkTime;
    }

    public function setLastTalkTime(?\DateTimeImmutable $lastTalkTime): self
    {
        $this->lastTalkTime = $lastTalkTime;
        return $this;
    }

    public function getUnionId(): ?string
    {
        return $this->unionId;
    }

    public function setUnionId(?string $unionId): self
    {
        $this->unionId = $unionId;
        return $this;
    }

    public function getUnfollowTime(): ?\DateTimeImmutable
    {
        return $this->unfollowTime;
    }

    public function setUnfollowTime(?\DateTimeImmutable $unfollowTime): self
    {
        $this->unfollowTime = $unfollowTime;
        return $this;
    }

    public function getUser(): User
    {
        return $this->user;
    }

    public function setUser(User $user): self
    {
        $this->user = $user;
        return $this;
    }

    /**
     * 判断是否为粉丝（已关注）
     */
    public function isFans(): bool
    {
        return $this->fansStatus === FansStatusEnum::FOLLOWED;
    }

    public function __toString(): string
    {
        return null !== $this->getId() 
            ? "{$this->getUser()->getNickNameDecrypted()}[{$this->getUnionId()}]" 
            : '';
    }
}
