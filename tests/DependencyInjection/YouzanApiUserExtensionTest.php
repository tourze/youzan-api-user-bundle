<?php

namespace YouzanApiUserBundle\Tests\DependencyInjection;

use PHPUnit\Framework\Attributes\CoversClass;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Tourze\PHPUnitSymfonyUnitTest\AbstractDependencyInjectionExtensionTestCase;
use YouzanApiUserBundle\DependencyInjection\YouzanApiUserExtension;

/**
 * @internal
 */
#[CoversClass(YouzanApiUserExtension::class)]
final class YouzanApiUserExtensionTest extends AbstractDependencyInjectionExtensionTestCase
{
    public function testContainerHasExtension(): void
    {
        $extension = new YouzanApiUserExtension();
        $this->assertInstanceOf(YouzanApiUserExtension::class, $extension);
    }

    public function testExtensionLoadsServices(): void
    {
        $container = new ContainerBuilder();
        $extension = new YouzanApiUserExtension();

        $this->assertInstanceOf(YouzanApiUserExtension::class, $extension);
        $this->assertEquals('youzan_api_user', $extension->getAlias());
    }
}
