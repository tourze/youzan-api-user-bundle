<?php

namespace YouzanApiUserBundle\DataFixtures;

use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use YouzanApiBundle\DataFixtures\AccountFixtures;
use YouzanApiBundle\Entity\Account;
use YouzanApiUserBundle\Entity\Follower;
use YouzanApiUserBundle\Entity\LevelInfo;
use YouzanApiUserBundle\Enum\GenderEnum;

class FollowerFixtures extends AppFixtures implements DependentFixtureInterface
{
    public const FOLLOWER_REFERENCE_PREFIX = 'follower_';
    public const FOLLOWER_COUNT = 30;

    public function load(ObjectManager $manager): void
    {
        for ($i = 0; $i < self::FOLLOWER_COUNT; ++$i) {
            $follower = new Follower();

            $follower->setUserId($this->faker->numberBetween(10000, 99999999));
            $follower->setWeixinOpenId($this->generateWeixinOpenId());
            $follower->setNick($this->faker->name());
            $follower->setAvatar($this->faker->imageUrl(150, 150, 'people'));
            $country = $this->faker->randomElement(['中国', '美国', '日本']);
            assert(is_string($country));
            $follower->setCountry($country);
            $province = $this->faker->randomElement(['北京', '上海', '广东', '浙江']);
            assert(is_string($province));
            $follower->setProvince($province);
            $follower->setCity($this->faker->city());
            $sex = $this->faker->randomElement(GenderEnum::cases());
            assert($sex instanceof GenderEnum);
            $follower->setSex($sex);
            $follower->setIsFollow($this->faker->boolean(80));
            $follower->setFollowTime($this->faker->unixTime());
            $follower->setTradedNum($this->faker->numberBetween(0, 50));
            $follower->setTradeMoney($this->faker->randomFloat(2, 0, 10000));
            $follower->setPoints(['total' => $this->faker->numberBetween(0, 1000), 'available' => $this->faker->numberBetween(0, 500)]);

            $accountRef = $this->faker->randomElement([
                AccountFixtures::ACCOUNT_MAIN_REFERENCE,
                AccountFixtures::ACCOUNT_TEST_REFERENCE,
                AccountFixtures::ACCOUNT_DEMO_REFERENCE,
            ]);
            assert(is_string($accountRef));
            $account = $this->getReference($accountRef, Account::class);
            $follower->setAccount($account);

            if ($this->faker->boolean(60)) {
                $levelInfoIndex = $this->faker->numberBetween(0, LevelInfoFixtures::LEVEL_INFO_COUNT - 1);
                $levelInfo = $this->getReference(LevelInfoFixtures::LEVEL_INFO_REFERENCE_PREFIX . $levelInfoIndex, LevelInfo::class);
                $follower->setLevelInfo($levelInfo);
            }

            $manager->persist($follower);
            $this->addReference(self::FOLLOWER_REFERENCE_PREFIX . $i, $follower);
        }

        $manager->flush();
    }

    public function getDependencies(): array
    {
        return [
            AccountFixtures::class,
            LevelInfoFixtures::class,
        ];
    }
}
