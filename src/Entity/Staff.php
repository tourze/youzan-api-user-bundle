<?php

namespace YouzanApiUserBundle\Entity;

use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Tourze\DoctrineTimestampBundle\Traits\TimestampableAware;
use YouzanApiUserBundle\Repository\StaffRepository;

/**
 * 有赞员工实体
 */
#[ORM\Entity(repositoryClass: StaffRepository::class)]
#[ORM\Table(name: 'ims_youzan_user_staff', options: ['comment' => '有赞员工表'])]
class Staff implements \Stringable
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

    #[ORM\Column(type: Types::STRING, length: 255, nullable: true, options: ['comment' => '企业名称'])]
    #[Assert\Length(max: 255)]
    private ?string $corpName = null;

    #[ORM\Column(type: Types::BIGINT, nullable: true, options: ['comment' => '微商城店铺ID'])]
    #[Assert\Positive]
    private ?int $kdtId = null;

    #[ORM\Column(type: Types::STRING, length: 64, nullable: true, options: ['comment' => '企业ID'])]
    #[Assert\Length(max: 64)]
    private ?string $corpId = null;

    #[ORM\Column(type: Types::STRING, length: 255, nullable: true, options: ['comment' => '员工邮箱'])]
    #[Assert\Length(max: 255)]
    #[Assert\Email]
    private ?string $email = null;

    #[ORM\Column(type: Types::STRING, length: 255, nullable: true, options: ['comment' => '员工名称'])]
    #[Assert\Length(max: 255)]
    private ?string $name = null;

    /**
     * 关联的有赞用户
     */
    #[ORM\OneToOne(targetEntity: User::class, cascade: ['persist'])]
    #[ORM\JoinColumn(name: 'user_id', referencedColumnName: 'id', nullable: false)]
    private User $user;

    public function getCorpName(): ?string
    {
        return $this->corpName;
    }

    public function setCorpName(?string $corpName): void
    {
        $this->corpName = $corpName;
    }

    public function getKdtId(): ?int
    {
        return $this->kdtId;
    }

    public function setKdtId(?int $kdtId): void
    {
        $this->kdtId = $kdtId;
    }

    public function getCorpId(): ?string
    {
        return $this->corpId;
    }

    public function setCorpId(?string $corpId): void
    {
        $this->corpId = $corpId;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(?string $email): void
    {
        $this->email = $email;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(?string $name): void
    {
        $this->name = $name;
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
        return null !== $this->getName() && null !== $this->getEmail()
            ? "{$this->getName()}[{$this->getEmail()}]"
            : '[]';
    }
}
