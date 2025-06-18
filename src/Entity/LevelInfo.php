<?php

namespace YouzanApiUserBundle\Entity;

use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Tourze\DoctrineTimestampBundle\Traits\TimestampableAware;
use YouzanApiUserBundle\Repository\LevelInfoRepository;

/**
 * 有赞用户等级信息实体
 */
#[ORM\Entity(repositoryClass: LevelInfoRepository::class)]
#[ORM\Table(name: 'ims_youzan_user_level_info', options: ['comment' => '有赞用户等级信息表'])]
class LevelInfo
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: Types::INTEGER, options: ['comment' => 'ID'])]
    private ?int $id = 0;

    public function getId(): ?int
    {
        return $this->id;
    }

    #[ORM\Column(type: 'bigint', nullable: true, options: ['comment' => '会员等级ID'])]
    private ?int $levelId = null;

    #[ORM\Column(type: 'string', length: 64, nullable: true, options: ['comment' => '会员等级名称'])]
    private ?string $levelName = null;

    use TimestampableAware;

    public function getLevelId(): ?int
    {
        return $this->levelId;
    }

    public function setLevelId(?int $levelId): self
    {
        $this->levelId = $levelId;
        return $this;
    }

    public function getLevelName(): ?string
    {
        return $this->levelName;
    }

    public function setLevelName(?string $levelName): self
    {
        $this->levelName = $levelName;
        return $this;
    }
}
