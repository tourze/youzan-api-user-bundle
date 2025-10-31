<?php

namespace YouzanApiUserBundle\DataFixtures;

use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use YouzanApiUserBundle\Entity\User;
use YouzanApiUserBundle\Entity\WechatInfo;
use YouzanApiUserBundle\Enum\FansStatusEnum;
use YouzanApiUserBundle\Enum\WechatTypeEnum;

class WechatInfoFixtures extends AppFixtures implements DependentFixtureInterface
{
    public const WECHAT_INFO_REFERENCE_PREFIX = 'wechat_info_';
    public const WECHAT_INFO_COUNT = 20; // 减少到不超过USER_COUNT以避免重复

    public function load(ObjectManager $manager): void
    {
        for ($i = 0; $i < self::WECHAT_INFO_COUNT; ++$i) {
            $wechatInfo = new WechatInfo();

            $wechatInfo->setWechatType($this->faker->randomElement(WechatTypeEnum::cases()));
            $wechatInfo->setFansStatus($this->faker->randomElement(FansStatusEnum::cases()));
            $wechatInfo->setUnionId($this->generateUnionId());

            if ($this->faker->boolean(80)) {
                $wechatInfo->setFollowTime(new \DateTimeImmutable($this->faker->dateTimeBetween('-1 year', 'now')->format('Y-m-d H:i:s')));
            }

            if ($this->faker->boolean(60)) {
                $wechatInfo->setLastTalkTime(new \DateTimeImmutable($this->faker->dateTimeBetween('-30 days', 'now')->format('Y-m-d H:i:s')));
            }

            if (FansStatusEnum::UNFOLLOWED === $wechatInfo->getFansStatus() && $this->faker->boolean(50)) {
                $wechatInfo->setUnfollowTime(new \DateTimeImmutable($this->faker->dateTimeBetween('-6 months', 'now')->format('Y-m-d H:i:s')));
            }

            // 确保每个WechatInfo对应唯一的User，避免unique constraint violation
            $userIndex = $i % UserFixtures::USER_COUNT;
            $user = $this->getReference(UserFixtures::USER_REFERENCE_PREFIX . $userIndex, User::class);
            $wechatInfo->setUser($user);

            $manager->persist($wechatInfo);
            $this->addReference(self::WECHAT_INFO_REFERENCE_PREFIX . $i, $wechatInfo);
        }

        $manager->flush();
    }

    public function getDependencies(): array
    {
        return [
            UserFixtures::class,
        ];
    }
}
