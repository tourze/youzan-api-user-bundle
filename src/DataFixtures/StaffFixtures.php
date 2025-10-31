<?php

namespace YouzanApiUserBundle\DataFixtures;

use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use YouzanApiUserBundle\Entity\Staff;
use YouzanApiUserBundle\Entity\User;

class StaffFixtures extends AppFixtures implements DependentFixtureInterface
{
    public const STAFF_REFERENCE_PREFIX = 'staff_';
    public const STAFF_COUNT = 10; // 减少到不超过USER_COUNT以避免重复

    public function load(ObjectManager $manager): void
    {
        for ($i = 0; $i < self::STAFF_COUNT; ++$i) {
            $staff = new Staff();

            $staff->setCorpName($this->faker->company());
            $staff->setKdtId($this->faker->numberBetween(100000, 999999));
            $staff->setCorpId($this->faker->regexify('[a-zA-Z0-9]{16,32}'));
            $staff->setEmail($this->faker->companyEmail());
            $staff->setName($this->faker->name());

            // 确保每个Staff对应唯一的User，避免unique constraint violation
            $userIndex = $i % UserFixtures::USER_COUNT;
            $user = $this->getReference(UserFixtures::USER_REFERENCE_PREFIX . $userIndex, User::class);
            $staff->setUser($user);

            $manager->persist($staff);
            $this->addReference(self::STAFF_REFERENCE_PREFIX . $i, $staff);
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
