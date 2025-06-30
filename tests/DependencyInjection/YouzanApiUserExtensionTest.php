<?php

namespace YouzanApiUserBundle\Tests\DependencyInjection;

use PHPUnit\Framework\TestCase;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use YouzanApiUserBundle\DependencyInjection\YouzanApiUserExtension;

class YouzanApiUserExtensionTest extends TestCase
{
    private YouzanApiUserExtension $extension;
    private ContainerBuilder $container;

    protected function setUp(): void
    {
        $this->extension = new YouzanApiUserExtension();
        $this->container = new ContainerBuilder();
    }

    public function testLoad(): void
    {
        $configs = [];
        $this->extension->load($configs, $this->container);

        // 验证服务是否正确加载
        $this->assertTrue($this->container->hasDefinition('YouzanApiUserBundle\Repository\UserRepository'));
        $this->assertTrue($this->container->hasDefinition('YouzanApiUserBundle\Repository\FollowerRepository'));
        $this->assertTrue($this->container->hasDefinition('YouzanApiUserBundle\Repository\StaffRepository'));
        $this->assertTrue($this->container->hasDefinition('YouzanApiUserBundle\Repository\LevelInfoRepository'));
        $this->assertTrue($this->container->hasDefinition('YouzanApiUserBundle\Repository\MobileInfoRepository'));
        $this->assertTrue($this->container->hasDefinition('YouzanApiUserBundle\Repository\WechatInfoRepository'));
        $this->assertTrue($this->container->hasDefinition('YouzanApiUserBundle\Command\SyncFollowersCommand'));
    }

    public function testGetAlias(): void
    {
        $this->assertEquals('youzan_api_user', $this->extension->getAlias());
    }
}