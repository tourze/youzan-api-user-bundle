<?php

namespace YouzanApiUserBundle\Entity;

use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Tourze\DoctrineTimestampBundle\Traits\TimestampableAware;
use YouzanApiBundle\Entity\Account;
use YouzanApiUserBundle\Enum\GenderEnum;
use YouzanApiUserBundle\Repository\UserRepository;

/**
 * 有赞用户实体
 */
#[ORM\Entity(repositoryClass: UserRepository::class)]
#[ORM\Table(name: 'ims_youzan_user', options: ['comment' => '有赞用户表'])]
class User
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: Types::INTEGER, options: ['comment' => 'ID'])]
    private ?int $id = 0;

    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * 基础信息 primitive_info
     */
    #[ORM\Column(type: 'string', length: 64, unique: true, options: ['comment' => '有赞用户ID'])]
    private string $yzOpenId;

    #[ORM\Column(type: 'string', length: 255, nullable: true, options: ['comment' => '昵称（加密）'])]
    private ?string $nickNameEncrypted = null;

    #[ORM\Column(type: 'string', length: 255, nullable: true, options: ['comment' => '昵称（明文）'])]
    private ?string $nickNameDecrypted = null;

    #[ORM\Column(type: 'string', length: 255, nullable: true, options: ['comment' => '头像'])]
    private ?string $avatar = null;

    #[ORM\Column(type: 'string', length: 32, nullable: true, options: ['comment' => '国家'])]
    private ?string $country = null;

    #[ORM\Column(type: 'string', length: 32, nullable: true, options: ['comment' => '省份'])]
    private ?string $province = null;

    #[ORM\Column(type: 'string', length: 32, nullable: true, options: ['comment' => '城市'])]
    private ?string $city = null;

    #[ORM\Column(type: 'integer', enumType: GenderEnum::class, options: ['comment' => '性别'])]
    private GenderEnum $gender = GenderEnum::UNKNOWN;

    #[ORM\Column(type: 'smallint', options: ['comment' => '平台类型'])]
    private int $platformType = 0;

    /**
     * 关联的员工信息
     */
    #[ORM\OneToOne(targetEntity: Staff::class, mappedBy: 'user')]
    private ?Staff $staff = null;

    /**
     * 关联的微信信息
     */
    #[ORM\OneToOne(targetEntity: WechatInfo::class, mappedBy: 'user')]
    private ?WechatInfo $wechatInfo = null;

    /**
     * 关联的手机信息
     */
    #[ORM\OneToOne(targetEntity: MobileInfo::class, mappedBy: 'user')]
    private ?MobileInfo $mobileInfo = null;

    /**
     * 关联的账号
     */
    #[ORM\ManyToOne(targetEntity: Account::class)]
    #[ORM\JoinColumn(name: 'account_id', referencedColumnName: 'id', nullable: false)]
    private Account $account;

    use TimestampableAware;

    public function getYzOpenId(): string
    {
        return $this->yzOpenId;
    }

    public function setYzOpenId(string $yzOpenId): self
    {
        $this->yzOpenId = $yzOpenId;
        return $this;
    }

    public function getNickNameEncrypted(): ?string
    {
        return $this->nickNameEncrypted;
    }

    public function setNickNameEncrypted(?string $nickNameEncrypted): self
    {
        $this->nickNameEncrypted = $nickNameEncrypted;
        return $this;
    }

    public function getNickNameDecrypted(): ?string
    {
        return $this->nickNameDecrypted;
    }

    public function setNickNameDecrypted(?string $nickNameDecrypted): self
    {
        $this->nickNameDecrypted = $nickNameDecrypted;
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

    public function getGender(): GenderEnum
    {
        return $this->gender;
    }

    public function setGender(GenderEnum $gender): self
    {
        $this->gender = $gender;
        return $this;
    }

    public function getPlatformType(): int
    {
        return $this->platformType;
    }

    public function setPlatformType(int $platformType): self
    {
        $this->platformType = $platformType;
        return $this;
    }

    public function getStaff(): ?Staff
    {
        return $this->staff;
    }

    public function setStaff(?Staff $staff): self
    {
        // 设置关联的双向引用
        $this->staff = $staff;
        if ($staff !== null && $staff->getUser() !== $this) {
            $staff->setUser($this);
        }
        return $this;
    }

    public function getWechatInfo(): ?WechatInfo
    {
        return $this->wechatInfo;
    }

    public function setWechatInfo(?WechatInfo $wechatInfo): self
    {
        // 设置关联的双向引用
        $this->wechatInfo = $wechatInfo;
        if ($wechatInfo !== null && $wechatInfo->getUser() !== $this) {
            $wechatInfo->setUser($this);
        }
        return $this;
    }

    public function getMobileInfo(): ?MobileInfo
    {
        return $this->mobileInfo;
    }

    public function setMobileInfo(?MobileInfo $mobileInfo): self
    {
        // 设置关联的双向引用
        $this->mobileInfo = $mobileInfo;
        if ($mobileInfo !== null && $mobileInfo->getUser() !== $this) {
            $mobileInfo->setUser($this);
        }
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
}
