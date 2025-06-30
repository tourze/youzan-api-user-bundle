<?php

namespace YouzanApiUserBundle\Tests\Entity;

use PHPUnit\Framework\TestCase;
use YouzanApiUserBundle\Entity\MobileInfo;
use YouzanApiUserBundle\Entity\User;

class MobileInfoTest extends TestCase
{
    private MobileInfo $mobileInfo;

    protected function setUp(): void
    {
        $this->mobileInfo = new MobileInfo();
    }

    public function testGettersAndSetters(): void
    {
        // Test User
        $user = new User();
        $this->mobileInfo->setUser($user);
        $this->assertSame($user, $this->mobileInfo->getUser());

        // Test countryCode
        $countryCode = '+86';
        $this->mobileInfo->setCountryCode($countryCode);
        $this->assertEquals($countryCode, $this->mobileInfo->getCountryCode());

        // Test mobileEncrypted
        $mobileEncrypted = 'encrypted_13800138000';
        $this->mobileInfo->setMobileEncrypted($mobileEncrypted);
        $this->assertEquals($mobileEncrypted, $this->mobileInfo->getMobileEncrypted());

        // Test mobileDecrypted
        $mobileDecrypted = '13800138000';
        $this->mobileInfo->setMobileDecrypted($mobileDecrypted);
        $this->assertEquals($mobileDecrypted, $this->mobileInfo->getMobileDecrypted());

        // Test null values
        $this->mobileInfo->setCountryCode(null);
        $this->assertNull($this->mobileInfo->getCountryCode());
        
        $this->mobileInfo->setMobileEncrypted(null);
        $this->assertNull($this->mobileInfo->getMobileEncrypted());
        
        $this->mobileInfo->setMobileDecrypted(null);
        $this->assertNull($this->mobileInfo->getMobileDecrypted());
    }

    public function testToString(): void
    {
        $user = new User();
        $this->mobileInfo->setUser($user);
        
        // 当 ID 为 0 时（默认值），应该返回 '-'
        $this->assertEquals('-', (string)$this->mobileInfo);

        $this->mobileInfo->setCountryCode('+86');
        $this->mobileInfo->setMobileDecrypted('13800138000');
        $this->assertEquals('+86-13800138000', (string)$this->mobileInfo);
    }
}