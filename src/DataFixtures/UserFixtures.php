<?php

namespace YouzanApiUserBundle\DataFixtures;

use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use YouzanApiBundle\DataFixtures\AccountFixtures;
use YouzanApiBundle\Entity\Account;
use YouzanApiUserBundle\Entity\User;
use YouzanApiUserBundle\Enum\GenderEnum;

class UserFixtures extends AppFixtures implements DependentFixtureInterface
{
    public const USER_REFERENCE_PREFIX = 'user_';
    public const USER_COUNT = 50;

    public function load(ObjectManager $manager): void
    {
        for ($i = 0; $i < self::USER_COUNT; ++$i) {
            $user = new User();

            $nickName = $this->faker->name();
            $user->setYzOpenId($this->generateYouzanOpenId());
            $user->setNickNameDecrypted($nickName);
            $user->setNickNameEncrypted($this->generateEncryptedData($nickName));
            $user->setAvatar($this->faker->imageUrl(200, 200, 'people'));
            $country = $this->faker->randomElement(['中国', '美国', '日本', '韩国']);
            assert(is_string($country));
            $user->setCountry($country);
            $province = $this->faker->randomElement(['北京', '上海', '广东', '浙江', '江苏']);
            assert(is_string($province));
            $user->setProvince($province);
            $user->setCity($this->faker->city());
            $gender = $this->faker->randomElement(GenderEnum::cases());
            assert($gender instanceof GenderEnum);
            $user->setGender($gender);
            $user->setPlatformType($this->faker->numberBetween(0, 3));

            $accountRef = $this->faker->randomElement([
                AccountFixtures::ACCOUNT_MAIN_REFERENCE,
                AccountFixtures::ACCOUNT_TEST_REFERENCE,
                AccountFixtures::ACCOUNT_DEMO_REFERENCE,
            ]);
            assert(is_string($accountRef));
            $account = $this->getReference($accountRef, Account::class);
            $user->setAccount($account);

            $manager->persist($user);
            $this->addReference(self::USER_REFERENCE_PREFIX . $i, $user);
        }

        $manager->flush();
    }

    public function getDependencies(): array
    {
        return [
            AccountFixtures::class,
        ];
    }
}
