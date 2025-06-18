<?php

namespace YouzanApiUserBundle\Entity;

use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Tourze\DoctrineTimestampBundle\Traits\TimestampableAware;
use YouzanApiBundle\Entity\Account;
use YouzanApiUserBundle\Enum\GenderEnum;
use YouzanApiUserBundle\Repository\FollowerRepository;

/**
 * 有赞粉丝实体
 */
#[ORM\Entity(repositoryClass: FollowerRepository::class)]
#[ORM\Table(name: 'ims_youzan_user_follower', options: ['comment' => '有赞粉丝表'])]
class Follower
{
    use TimestampableAware;
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: Types::INTEGER, options: ['comment' => 'ID'])]
    private ?int $id = 0;

    #[ORM\Column(type: 'bigint', unique: true, options: ['comment' => '有赞用户ID'])]
    private int $userId;

    #[ORM\Column(type: 'string', length: 64, unique: true, options: ['comment' => '微信 OpenID'])]
    private string $weixinOpenId;

    #[ORM\Column(type: 'string', length: 255, nullable: true, options: ['comment' => '昵称'])]
    private ?string $nick = null;

    #[ORM\Column(type: 'string', length: 255, nullable: true, options: ['comment' => '头像'])]
    private ?string $avatar = null;

    #[ORM\Column(type: 'string', length: 32, nullable: true, options: ['comment' => '国家'])]
    private ?string $country = null;

    #[ORM\Column(type: 'string', length: 32, nullable: true, options: ['comment' => '省份'])]
    private ?string $province = null;

    #[ORM\Column(type: 'string', length: 32, nullable: true, options: ['comment' => '城市'])]
    private ?string $city = null;

    #[ORM\Column(type: 'integer', enumType: GenderEnum::class, options: ['comment' => '性别'])]
    private GenderEnum $sex = GenderEnum::UNKNOWN;

    #[ORM\Column(type: 'boolean', options: ['comment' => '是否关注'])]
    private bool $isFollow = false;

    #[ORM\Column(type: 'integer', nullable: true, options: ['comment' => '关注时间'])]
    private ?int $followTime = null;

    #[ORM\Column(type: 'integer', nullable: true, options: ['comment' => '交易笔数'])]
    private ?int $tradedNum = null;

    #[ORM\Column(type: 'decimal', precision: 10, scale: 2, nullable: true, options: ['comment' => '交易金额'])]
    private ?float $tradeMoney = null;

    #[ORM\Column(type: 'json', nullable: true, options: ['comment' => '积分信息'])]
    private ?array $points = null;

    /**
     * 关联的账号
     */
    #[ORM\ManyToOne(targetEntity: Account::class)]
    #[ORM\JoinColumn(name: 'account_id', referencedColumnName: 'id', nullable: false)]
    private Account $account;

    /**
     * 关联的等级信息
     */
    #[ORM\ManyToOne(targetEntity: LevelInfo::class)]
    #[ORM\JoinColumn(name: 'level_info_id', referencedColumnName: 'id', nullable: true)]
    private ?LevelInfo $levelInfo = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUserId(): int
    {
        return $this->userId;
    }

    public function setUserId(int $userId): self
    {
        $this->userId = $userId;
        return $this;
    }

    public function getWeixinOpenId(): string
    {
        return $this->weixinOpenId;
    }

    public function setWeixinOpenId(string $weixinOpenId): self
    {
        $this->weixinOpenId = $weixinOpenId;
        return $this;
    }

    public function getNick(): ?string
    {
        return $this->nick;
    }

    public function setNick(?string $nick): self
    {
        $this->nick = $nick;
        return $this;
    }

    public function getAvatar(): ?string
    {
        return $this->avatar;
    }

    public function setAvatar(?string $avatar): self
    {
        $this->avatar = $avatar;
        return $this;
    }

    public function getCountry(): ?string
    {
        return $this->country;
    }

    public function setCountry(?string $country): self
    {
        $this->country = $country;
        return $this;
    }

    public function getProvince(): ?string
    {
        return $this->province;
    }

    public function setProvince(?string $province): self
    {
        $this->province = $province;
        return $this;
    }

    public function getCity(): ?string
    {
        return $this->city;
    }

    public function setCity(?string $city): self
    {
        $this->city = $city;
        return $this;
    }

    public function getSex(): GenderEnum
    {
        return $this->sex;
    }

    public function setSex(GenderEnum $sex): self
    {
        $this->sex = $sex;
        return $this;
    }

    public function isFollow(): bool
    {
        return $this->isFollow;
    }

    public function setIsFollow(bool $isFollow): self
    {
        $this->isFollow = $isFollow;
        return $this;
    }

    public function getFollowTime(): ?int
    {
        return $this->followTime;
    }

    public function setFollowTime(?int $followTime): self
    {
        $this->followTime = $followTime;
        return $this;
    }

    public function getTradedNum(): ?int
    {
        return $this->tradedNum;
    }

    public function setTradedNum(?int $tradedNum): self
    {
        $this->tradedNum = $tradedNum;
        return $this;
    }

    public function getTradeMoney(): ?float
    {
        return $this->tradeMoney;
    }

    public function setTradeMoney(?float $tradeMoney): self
    {
        $this->tradeMoney = $tradeMoney;
        return $this;
    }

    public function getPoints(): ?array
    {
        return $this->points;
    }

    public function setPoints(?array $points): self
    {
        $this->points = $points;
        return $this;
    }

    public function getAccount(): Account
    {
        return $this->account;
    }

    public function setAccount(?Account $account): self
    {
        $this->account = $account;
        return $this;
    }

    public function getLevelInfo(): ?LevelInfo
    {
        return $this->levelInfo;
    }

    public function setLevelInfo(?LevelInfo $levelInfo): self
    {
        $this->levelInfo = $levelInfo;
        return $this;
    }}
