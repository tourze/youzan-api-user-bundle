<?php

namespace YouzanApiUserBundle\Entity;

use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Tourze\DoctrineTimestampBundle\Traits\TimestampableAware;
use YouzanApiUserBundle\Repository\MobileInfoRepository;

/**
 * 有赞用户手机信息实体
 */
#[ORM\Entity(repositoryClass: MobileInfoRepository::class)]
#[ORM\Table(name: 'ims_youzan_user_mobile_info', options: ['comment' => '有赞用户手机信息表'])]
class MobileInfo implements \Stringable
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: Types::INTEGER, options: ['comment' => 'ID'])]
    private ?int $id = 0;

    public function getId(): ?int
    {
        return $this->id;
    }

    #[ORM\Column(type: Types::STRING, length: 32, nullable: true, options: ['comment' => '国家代码'])]
    private ?string $countryCode = null;

    #[ORM\Column(type: Types::STRING, length: 32, nullable: true, options: ['comment' => '手机号（加密）'])]
    private ?string $mobileEncrypted = null;

    #[ORM\Column(type: Types::STRING, length: 32, nullable: true, options: ['comment' => '手机号（明文）'])]
    private ?string $mobileDecrypted = null;

    /**
     * 关联的有赞用户
     */
    #[ORM\OneToOne(targetEntity: User::class, inversedBy: 'mobileInfo')]
    #[ORM\JoinColumn(name: 'user_id', referencedColumnName: 'id', nullable: false)]
    private User $user;

    use TimestampableAware;

    public function getCountryCode(): ?string
    {
        return $this->countryCode;
    }

    public function setCountryCode(?string $countryCode): self
    {
        $this->countryCode = $countryCode;
        return $this;
    }

    public function getMobileEncrypted(): ?string
    {
        return $this->mobileEncrypted;
    }

    public function setMobileEncrypted(?string $mobileEncrypted): self
    {
        $this->mobileEncrypted = $mobileEncrypted;
        return $this;
    }

    public function getMobileDecrypted(): ?string
    {
        return $this->mobileDecrypted;
    }

    public function setMobileDecrypted(?string $mobileDecrypted): self
    {
        $this->mobileDecrypted = $mobileDecrypted;
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

    public function __toString(): string
    {
        return null !== $this->getId() 
            ? "{$this->getCountryCode()}-{$this->getMobileDecrypted()}" 
            : '';
    }
}
