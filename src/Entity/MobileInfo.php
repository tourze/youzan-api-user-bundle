<?php

namespace YouzanApiUserBundle\Entity;

use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Tourze\DoctrineTimestampBundle\Traits\TimestampableAware;
use YouzanApiUserBundle\Repository\MobileInfoRepository;

/**
 * 有赞用户手机信息实体
 */
#[ORM\Entity(repositoryClass: MobileInfoRepository::class)]
#[ORM\Table(name: 'ims_youzan_user_mobile_info', options: ['comment' => '有赞用户手机信息表'])]
class MobileInfo implements \Stringable
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

    #[ORM\Column(type: Types::STRING, length: 32, nullable: true, options: ['comment' => '国家代码'])]
    #[Assert\Length(max: 32)]
    private ?string $countryCode = null;

    #[ORM\Column(type: Types::STRING, length: 32, nullable: true, options: ['comment' => '手机号（加密）'])]
    #[Assert\Length(max: 32)]
    #[Assert\Regex(pattern: '/^[a-zA-Z0-9+\/=]*$/', message: 'Encrypted data should contain only base64 characters')]
    private ?string $mobileEncrypted = null;

    #[ORM\Column(type: Types::STRING, length: 32, nullable: true, options: ['comment' => '手机号（明文）'])]
    #[Assert\Length(max: 32)]
    #[Assert\Regex(pattern: '/^1[3-9]\d{9}$/', message: 'Mobile phone number must be a valid Chinese mobile number')]
    private ?string $mobileDecrypted = null;

    /**
     * 关联的有赞用户（单向关联）
     */
    #[ORM\OneToOne(targetEntity: User::class, cascade: ['persist'])]
    #[ORM\JoinColumn(name: 'user_id', referencedColumnName: 'id', nullable: false)]
    private User $user;

    public function getCountryCode(): ?string
    {
        return $this->countryCode;
    }

    public function setCountryCode(?string $countryCode): void
    {
        $this->countryCode = $countryCode;
    }

    public function getMobileEncrypted(): ?string
    {
        return $this->mobileEncrypted;
    }

    public function setMobileEncrypted(?string $mobileEncrypted): void
    {
        $this->mobileEncrypted = $mobileEncrypted;
    }

    public function getMobileDecrypted(): ?string
    {
        return $this->mobileDecrypted;
    }

    public function setMobileDecrypted(?string $mobileDecrypted): void
    {
        $this->mobileDecrypted = $mobileDecrypted;
    }

    public function getUser(): User
    {
        return $this->user;
    }

    public function setUser(User $user): void
    {
        $this->user = $user;
    }

    public function __toString(): string
    {
        return null !== $this->getCountryCode() && null !== $this->getMobileDecrypted()
            ? "{$this->getCountryCode()}-{$this->getMobileDecrypted()}"
            : '-';
    }
}
