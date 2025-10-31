<?php

namespace YouzanApiUserBundle\Entity;

use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Tourze\DoctrineTimestampBundle\Traits\TimestampableAware;
use YouzanApiBundle\Entity\Account;
use YouzanApiUserBundle\Enum\GenderEnum;
use YouzanApiUserBundle\Repository\FollowerRepository;

/**
 * 有赞粉丝实体
 */
#[ORM\Entity(repositoryClass: FollowerRepository::class)]
#[ORM\Table(name: 'ims_youzan_user_follower', options: ['comment' => '有赞籉丝表'])]
class Follower implements \Stringable
{
    use TimestampableAware;

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: Types::INTEGER, options: ['comment' => 'ID'])]
    private int $id = 0;

    #[ORM\Column(type: Types::BIGINT, unique: true, options: ['comment' => '有赞用户ID'])]
    #[Assert\NotBlank]
    #[Assert\Positive]
    private int $userId;

    #[ORM\Column(type: Types::STRING, length: 64, unique: true, options: ['comment' => '微信 OpenID'])]
    #[Assert\NotBlank]
    #[Assert\Length(max: 64)]
    private string $weixinOpenId;

    #[ORM\Column(type: Types::STRING, length: 255, nullable: true, options: ['comment' => '昵称'])]
    #[Assert\Length(max: 255)]
    private ?string $nick = null;

    #[ORM\Column(type: Types::STRING, length: 255, nullable: true, options: ['comment' => '头像'])]
    #[Assert\Length(max: 255)]
    #[Assert\Url]
    private ?string $avatar = null;

    #[ORM\Column(type: Types::STRING, length: 32, nullable: true, options: ['comment' => '国家'])]
    #[Assert\Length(max: 32)]
    private ?string $country = null;

    #[ORM\Column(type: Types::STRING, length: 32, nullable: true, options: ['comment' => '省份'])]
    #[Assert\Length(max: 32)]
    private ?string $province = null;

    #[ORM\Column(type: Types::STRING, length: 32, nullable: true, options: ['comment' => '城市'])]
    #[Assert\Length(max: 32)]
    private ?string $city = null;

    #[ORM\Column(type: Types::INTEGER, enumType: GenderEnum::class, options: ['comment' => '性别'])]
    #[Assert\Choice(callback: [GenderEnum::class, 'cases'])]
    private GenderEnum $sex = GenderEnum::UNKNOWN;

    #[ORM\Column(type: Types::BOOLEAN, options: ['comment' => '是否关注'])]
    #[Assert\Type(type: 'bool', message: 'isFollow must be a boolean value')]
    private bool $isFollow = false;

    #[ORM\Column(type: Types::INTEGER, nullable: true, options: ['comment' => '关注时间'])]
    #[Assert\PositiveOrZero]
    private ?int $followTime = null;

    #[ORM\Column(type: Types::INTEGER, nullable: true, options: ['comment' => '交易笔数'])]
    #[Assert\PositiveOrZero]
    private ?int $tradedNum = null;

    #[ORM\Column(type: Types::DECIMAL, precision: 10, scale: 2, nullable: true, options: ['comment' => '交易金额'])]
    #[Assert\PositiveOrZero]
    private ?float $tradeMoney = null;

    /**
     * @var array<string, mixed>|null
     */
    #[ORM\Column(type: Types::JSON, nullable: true, options: ['comment' => '积分信息'])]
    #[Assert\Type(type: 'array', message: 'Points must be an array')]
    private ?array $points = null;

    /**
     * 关联的账号
     */
    #[ORM\ManyToOne(targetEntity: Account::class, cascade: ['persist'])]
    #[ORM\JoinColumn(name: 'account_id', referencedColumnName: 'id', nullable: false)]
    private Account $account;

    /**
     * 关联的等级信息
     */
    #[ORM\ManyToOne(targetEntity: LevelInfo::class, cascade: ['persist'])]
    #[ORM\JoinColumn(name: 'level_info_id', referencedColumnName: 'id', nullable: true)]
    private ?LevelInfo $levelInfo = null;

    public function getId(): int
    {
        return $this->id;
    }

    public function getUserId(): int
    {
        return $this->userId;
    }

    public function setUserId(int $userId): void
    {
        $this->userId = $userId;
    }

    public function getWeixinOpenId(): string
    {
        return $this->weixinOpenId;
    }

    public function setWeixinOpenId(string $weixinOpenId): void
    {
        $this->weixinOpenId = $weixinOpenId;
    }

    public function getNick(): ?string
    {
        return $this->nick;
    }

    public function setNick(?string $nick): void
    {
        $this->nick = $nick;
    }

    public function getAvatar(): ?string
    {
        return $this->avatar;
    }

    public function setAvatar(?string $avatar): void
    {
        $this->avatar = $avatar;
    }

    public function getCountry(): ?string
    {
        return $this->country;
    }

    public function setCountry(?string $country): void
    {
        $this->country = $country;
    }

    public function getProvince(): ?string
    {
        return $this->province;
    }

    public function setProvince(?string $province): void
    {
        $this->province = $province;
    }

    public function getCity(): ?string
    {
        return $this->city;
    }

    public function setCity(?string $city): void
    {
        $this->city = $city;
    }

    public function getSex(): GenderEnum
    {
        return $this->sex;
    }

    public function setSex(GenderEnum $sex): void
    {
        $this->sex = $sex;
    }

    public function isFollow(): bool
    {
        return $this->isFollow;
    }

    public function setIsFollow(bool $isFollow): void
    {
        $this->isFollow = $isFollow;
    }

    public function getFollowTime(): ?int
    {
        return $this->followTime;
    }

    public function setFollowTime(?int $followTime): void
    {
        $this->followTime = $followTime;
    }

    public function getTradedNum(): ?int
    {
        return $this->tradedNum;
    }

    public function setTradedNum(?int $tradedNum): void
    {
        $this->tradedNum = $tradedNum;
    }

    public function getTradeMoney(): ?float
    {
        return $this->tradeMoney;
    }

    public function setTradeMoney(?float $tradeMoney): void
    {
        $this->tradeMoney = $tradeMoney;
    }

    /**
     * @return array<string, mixed>|null
     */
    public function getPoints(): ?array
    {
        return $this->points;
    }

    /**
     * @param array<string, mixed>|null $points
     */
    public function setPoints(?array $points): void
    {
        $this->points = $points;
    }

    public function getAccount(): Account
    {
        return $this->account;
    }

    public function setAccount(Account $account): void
    {
        $this->account = $account;
    }

    public function getLevelInfo(): ?LevelInfo
    {
        return $this->levelInfo;
    }

    public function setLevelInfo(?LevelInfo $levelInfo): void
    {
        $this->levelInfo = $levelInfo;
    }

    public function __toString(): string
    {
        return 0 !== $this->getId()
            ? "{$this->getNick()}[{$this->getWeixinOpenId()}]"
            : '';
    }
}
