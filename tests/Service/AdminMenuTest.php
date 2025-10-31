<?php

declare(strict_types=1);

namespace YouzanApiUserBundle\Tests\Service;

use Knp\Menu\MenuFactory;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\RunTestsInSeparateProcesses;
use Tourze\PHPUnitSymfonyWebTest\AbstractEasyAdminMenuTestCase;
use YouzanApiUserBundle\Service\AdminMenu;

/**
 * AdminMenu服务测试
 * @internal
 */
#[CoversClass(AdminMenu::class)]
#[RunTestsInSeparateProcesses]
class AdminMenuTest extends AbstractEasyAdminMenuTestCase
{
    protected function onSetUp(): void
    {
        // Setup for AdminMenu tests
    }

    public function testInvokeAddsMenuItems(): void
    {
        $container = self::getContainer();
        /** @var AdminMenu $adminMenu */
        $adminMenu = $container->get(AdminMenu::class);

        $factory = new MenuFactory();
        $rootItem = $factory->createItem('root');

        $adminMenu->__invoke($rootItem);

        // 验证主菜单
        $youzanMenu = $rootItem->getChild('有赞用户管理');
        self::assertNotNull($youzanMenu);
        self::assertSame('fas fa-users', $youzanMenu->getAttribute('icon'));

        // 验证核心用户管理子菜单
        $userMenu = $youzanMenu->getChild('有赞用户');
        self::assertNotNull($userMenu);
        self::assertSame('fas fa-user', $userMenu->getAttribute('icon'));

        $followerMenu = $youzanMenu->getChild('粉丝管理');
        self::assertNotNull($followerMenu);
        self::assertSame('fas fa-heart', $followerMenu->getAttribute('icon'));

        // 验证用户详细信息子菜单组
        $detailMenu = $youzanMenu->getChild('用户详细信息');
        self::assertNotNull($detailMenu);
        self::assertSame('fas fa-address-card', $detailMenu->getAttribute('icon'));

        // 验证详细信息子菜单项
        $staffMenu = $detailMenu->getChild('员工信息');
        self::assertNotNull($staffMenu);
        self::assertSame('fas fa-id-card', $staffMenu->getAttribute('icon'));

        $wechatMenu = $detailMenu->getChild('微信信息');
        self::assertNotNull($wechatMenu);
        self::assertSame('fab fa-weixin', $wechatMenu->getAttribute('icon'));

        $mobileMenu = $detailMenu->getChild('手机信息');
        self::assertNotNull($mobileMenu);
        self::assertSame('fas fa-mobile-alt', $mobileMenu->getAttribute('icon'));

        // 验证系统配置子菜单
        $configMenu = $youzanMenu->getChild('系统配置');
        self::assertNotNull($configMenu);
        self::assertSame('fas fa-cog', $configMenu->getAttribute('icon'));

        $levelMenu = $configMenu->getChild('会员等级');
        self::assertNotNull($levelMenu);
        self::assertSame('fas fa-star', $levelMenu->getAttribute('icon'));
    }

    public function testMenuStructure(): void
    {
        $container = self::getContainer();
        /** @var AdminMenu $adminMenu */
        $adminMenu = $container->get(AdminMenu::class);

        $factory = new MenuFactory();
        $rootItem = $factory->createItem('root');

        $adminMenu->__invoke($rootItem);

        // 验证菜单层次结构
        $youzanMenu = $rootItem->getChild('有赞用户管理');
        self::assertNotNull($youzanMenu);

        // 验证二级菜单数量
        $children = $youzanMenu->getChildren();
        self::assertCount(4, $children); // 有赞用户, 粉丝管理, 用户详细信息, 系统配置

        // 验证详细信息子菜单数量
        $detailMenu = $youzanMenu->getChild('用户详细信息');
        if (null !== $detailMenu) {
            $detailChildren = $detailMenu->getChildren();
            self::assertCount(3, $detailChildren); // 员工信息, 微信信息, 手机信息
        }

        // 验证配置子菜单数量
        $configMenu = $youzanMenu->getChild('系统配置');
        if (null !== $configMenu) {
            $configChildren = $configMenu->getChildren();
            self::assertCount(1, $configChildren); // 会员等级
        }
    }
}
