<?php

namespace YouzanApiUserBundle\Tests\Entity;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\RunTestsInSeparateProcesses;
use PHPUnit\Framework\TestCase;
use Tourze\PHPUnitDoctrineEntity\AbstractEntityTestCase;
use YouzanApiUserBundle\Entity\MobileInfo;
use YouzanApiUserBundle\Entity\User;

/**
 * @internal
 */
#[CoversClass(MobileInfo::class)]
final class MobileInfoTest extends AbstractEntityTestCase
{
    protected function createEntity(): object
    {
        return new MobileInfo();
    }

    /**
     * @return iterable<string, array{string, mixed}>
     */
    public static function propertiesProvider(): iterable
    {
        $user = new User();

        return [
            'user' => ['user', $user],
            'countryCode' => ['countryCode', '+86'],
            'countryCode-null' => ['countryCode', null],
            'mobileEncrypted' => ['mobileEncrypted', 'encrypted_13800138000'],
            'mobileEncrypted-null' => ['mobileEncrypted', null],
            'mobileDecrypted' => ['mobileDecrypted', '13800138000'],
            'mobileDecrypted-null' => ['mobileDecrypted', null],
        ];
    }

    public function testToString(): void
    {
        $mobileInfo = new MobileInfo();

        // 当 ID 为 0 时（默认值），应该返回 '-'
        $this->assertEquals('-', (string) $mobileInfo);

        $mobileInfo->setCountryCode('+86');
        $mobileInfo->setMobileDecrypted('13800138000');
        $this->assertEquals('+86-13800138000', (string) $mobileInfo);
    }
}
