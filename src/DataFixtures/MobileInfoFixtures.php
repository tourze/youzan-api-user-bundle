<?php

namespace YouzanApiUserBundle\DataFixtures;

use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use YouzanApiUserBundle\Entity\MobileInfo;
use YouzanApiUserBundle\Entity\User;

class MobileInfoFixtures extends AppFixtures implements DependentFixtureInterface
{
    public const MOBILE_INFO_REFERENCE_PREFIX = 'mobile_info_';
    public const MOBILE_INFO_COUNT = 20;

    public function load(ObjectManager $manager): void
    {
        for ($i = 0; $i < self::MOBILE_INFO_COUNT; ++$i) {
            $mobileInfo = new MobileInfo();

            $mobileNumber = $this->generateChineseMobileNumber();
            $mobileInfo->setCountryCode('+86');
            $mobileInfo->setMobileDecrypted($mobileNumber);
            $mobileInfo->setMobileEncrypted($this->generateEncryptedData($mobileNumber));

            // 确保每个MobileInfo对应唯一的User，避免unique constraint violation
            $userIndex = $i % UserFixtures::USER_COUNT;
            $user = $this->getReference(UserFixtures::USER_REFERENCE_PREFIX . $userIndex, User::class);
            $mobileInfo->setUser($user);

            $manager->persist($mobileInfo);
            $this->addReference(self::MOBILE_INFO_REFERENCE_PREFIX . $i, $mobileInfo);
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
