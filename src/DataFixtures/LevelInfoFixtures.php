<?php

namespace YouzanApiUserBundle\DataFixtures;

use Doctrine\Persistence\ObjectManager;
use YouzanApiUserBundle\Entity\LevelInfo;

final class LevelInfoFixtures extends AppFixtures
{
    public const LEVEL_INFO_REFERENCE_PREFIX = 'level_info_';
    public const LEVEL_INFO_COUNT = 10;

    public function load(ObjectManager $manager): void
    {
        for ($i = 0; $i < self::LEVEL_INFO_COUNT; ++$i) {
            $levelInfo = new LevelInfo();
            $levelInfo->setLevelId($this->faker->numberBetween(1, 10));
            $levelInfo->setLevelName($this->faker->randomElement([
                '普通会员', '银卡会员', '金卡会员', '白金会员', '钻石会员',
                'VIP1', 'VIP2', 'VIP3', 'VIP4', 'VIP5',
            ]));

            $manager->persist($levelInfo);
            $this->addReference(self::LEVEL_INFO_REFERENCE_PREFIX . $i, $levelInfo);
        }

        $manager->flush();
    }
}
