<?php

namespace YouzanApiUserBundle\Entity;

use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Tourze\DoctrineTimestampBundle\Traits\TimestampableAware;
use YouzanApiBundle\Entity\Account;
use YouzanApiUserBundle\Enum\GenderEnum;
use YouzanApiUserBundle\Repository\UserRepository;

/**
 * 有赞用户实体
 */
#[ORM\Entity(repositoryClass: UserRepository::class)]
#[ORM\Table(name: 'ims_youzan_user', options: ['comment' => '有赞用户表'])]
class User implements \Stringable
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

    #[ORM\Column(type: Types::STRING, length: 64, unique: true, options: ['comment' => '有赞用户ID'])]
    #[Assert\NotBlank]
    #[Assert\Length(max: 64)]
    private string $yzOpenId;

    #[ORM\Column(type: Types::STRING, length: 255, nullable: true, options: ['comment' => '昵称（加密）'])]
    #[Assert\Length(max: 255)]
    private ?string $nickNameEncrypted = null;

    #[ORM\Column(type: Types::STRING, length: 255, nullable: true, options: ['comment' => '昵称（明文）'])]
    #[Assert\Length(max: 255)]
    private ?string $nickNameDecrypted = null;

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
    #[Assert\Choice(callback: [GenderEnum::class, 'cases'], message: 'Invalid gender value')]
    private GenderEnum $gender = GenderEnum::UNKNOWN;

    #[ORM\Column(type: Types::SMALLINT, options: ['comment' => '平台类型'])]
    #[Assert\GreaterThanOrEqual(value: 0, message: 'Platform type must be non-negative')]
    private int $platformType = 0;

    /**
     * 关联的员工信息
     * 这些关联已转换为单向关联，不再需要反向端属性
     * 如需查询，请使用对应的 Repository
     */

    /**
     * 关联的微信信息
     * 这些关联已转换为单向关联，不再需要反向端属性
     * 如需查询，请使用对应的 Repository
     */

    /**
     * 关联的手机信息
     * 这些关联已转换为单向关联，不再需要反向端属性
     * 如需查询，请使用对应的 Repository
     */

    /**
     * 关联的账号
     */
    #[ORM\ManyToOne(targetEntity: Account::class, cascade: ['persist'])]
    #[ORM\JoinColumn(name: 'account_id', referencedColumnName: 'id', nullable: false)]
    private Account $account;

    public function getYzOpenId(): string
    {
        return $this->yzOpenId;
    }

    public function setYzOpenId(string $yzOpenId): void
    {
        $this->yzOpenId = $yzOpenId;
    }

    public function getNickNameEncrypted(): ?string
    {
        return $this->nickNameEncrypted;
    }

    public function setNickNameEncrypted(?string $nickNameEncrypted): void
    {
        $this->nickNameEncrypted = $nickNameEncrypted;
    }

    public function getNickNameDecrypted(): ?string
    {
        return $this->nickNameDecrypted;
    }

    public function setNickNameDecrypted(?string $nickNameDecrypted): void
    {
        $this->nickNameDecrypted = $nickNameDecrypted;
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

    public function getGender(): GenderEnum
    {
        return $this->gender;
    }

    public function setGender(GenderEnum $gender): void
    {
        $this->gender = $gender;
    }

    public function getPlatformType(): int
    {
        return $this->platformType;
    }

    public function setPlatformType(int $platformType): void
    {
        $this->platformType = $platformType;
    }

    public function getAccount(): Account
    {
        return $this->account;
    }

    public function setAccount(Account $account): void
    {
        $this->account = $account;
    }

    public function __toString(): string
    {
        try {
            return isset($this->id) && 0 !== $this->id
                ? "{$this->getNickNameDecrypted()}[{$this->getYzOpenId()}]"
                : '';
        } catch (\Error $e) {
            // 如果属性未初始化，返回空字符串
            return '';
        }
    }
}
