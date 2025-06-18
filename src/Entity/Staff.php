<?php

namespace YouzanApiUserBundle\Entity;

use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Tourze\DoctrineTimestampBundle\Traits\TimestampableAware;
use YouzanApiUserBundle\Repository\StaffRepository;

/**
 * 有赞员工实体
 */
#[ORM\Entity(repositoryClass: StaffRepository::class)]
#[ORM\Table(name: 'ims_youzan_user_staff', options: ['comment' => '有赞员工表'])]
class Staff
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: Types::INTEGER, options: ['comment' => 'ID'])]
    private ?int $id = 0;

    public function getId(): ?int
    {
        return $this->id;
    }

    #[ORM\Column(type: 'string', length: 255, nullable: true, options: ['comment' => '企业名称'])]
    private ?string $corpName = null;

    #[ORM\Column(type: 'bigint', nullable: true, options: ['comment' => '微商城店铺ID'])]
    private ?int $kdtId = null;

    #[ORM\Column(type: 'string', length: 64, nullable: true, options: ['comment' => '企业ID'])]
    private ?string $corpId = null;

    #[ORM\Column(type: 'string', length: 255, nullable: true, options: ['comment' => '员工邮箱'])]
    private ?string $email = null;

    #[ORM\Column(type: 'string', length: 255, nullable: true, options: ['comment' => '员工名称'])]
    private ?string $name = null;

    /**
     * 关联的有赞用户
     */
    #[ORM\OneToOne(targetEntity: User::class, inversedBy: 'staff')]
    #[ORM\JoinColumn(name: 'user_id', referencedColumnName: 'id', nullable: false)]
    private User $user;

    use TimestampableAware;

    public function getCorpName(): ?string
    {
        return $this->corpName;
    }

    public function setCorpName(?string $corpName): self
    {
        $this->corpName = $corpName;
        return $this;
    }

    public function getKdtId(): ?int
    {
        return $this->kdtId;
    }

    public function setKdtId(?int $kdtId): self
    {
        $this->kdtId = $kdtId;
        return $this;
    }

    public function getCorpId(): ?string
    {
        return $this->corpId;
    }

    public function setCorpId(?string $corpId): self
    {
        $this->corpId = $corpId;
        return $this;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(?string $email): self
    {
        $this->email = $email;
        return $this;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(?string $name): self
    {
        $this->name = $name;
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
}
