<?php

namespace YouzanApiUserBundle\Entity;

use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Tourze\DoctrineTimestampBundle\Traits\TimestampableAware;
use YouzanApiUserBundle\Repository\LevelInfoRepository;

/**
 * 有赞用户等级信息实体
 */
#[ORM\Entity(repositoryClass: LevelInfoRepository::class)]
#[ORM\Table(name: 'ims_youzan_user_level_info', options: ['comment' => '有赞用户等级信息表'])]
class LevelInfo implements \Stringable
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

    #[ORM\Column(type: Types::BIGINT, nullable: true, options: ['comment' => '会员等级ID'])]
    #[Assert\Positive]
    private ?int $levelId = null;

    #[ORM\Column(type: Types::STRING, length: 64, nullable: true, options: ['comment' => '会员等级名称'])]
    #[Assert\Length(max: 64)]
    private ?string $levelName = null;

    public function getLevelId(): ?int
    {
        return $this->levelId;
    }

    public function setLevelId(?int $levelId): void
    {
        $this->levelId = $levelId;
    }

    public function getLevelName(): ?string
    {
        return $this->levelName;
    }

    public function setLevelName(?string $levelName): void
    {
        $this->levelName = $levelName;
    }

    public function __toString(): string
    {
        return null !== $this->getLevelName() && null !== $this->getLevelId()
            ? "{$this->getLevelName()}[{$this->getLevelId()}]"
            : '[]';
    }
}
