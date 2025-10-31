<?php

declare(strict_types=1);

namespace YouzanApiUserBundle\Tests\Controller\Admin;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\RunTestsInSeparateProcesses;
use Tourze\PHPUnitSymfonyWebTest\AbstractEasyAdminControllerTestCase;
use YouzanApiUserBundle\Controller\Admin\WechatInfoCrudController;
use YouzanApiUserBundle\Entity\WechatInfo;

/**
 * @internal
 */
#[CoversClass(WechatInfoCrudController::class)]
#[RunTestsInSeparateProcesses]
final class WechatInfoCrudControllerTest extends AbstractEasyAdminControllerTestCase
{
    protected function getControllerService(): WechatInfoCrudController
    {
        return self::getService(WechatInfoCrudController::class);
    }

    /** @return iterable<string, array{string}> */
    public static function provideIndexPageHeaders(): iterable
    {
        return [
            'ID' => ['ID'],
            '微信类型' => ['微信类型'],
            '粉丝状态' => ['粉丝状态'],
            '关联用户' => ['关联用户'],
        ];
    }

    /** @return iterable<string, array{string}> */
    public static function provideNewPageFields(): iterable
    {
        yield 'wechatType' => ['wechatType'];
        yield 'fansStatus' => ['fansStatus'];
        yield 'unionId' => ['unionId'];
        yield 'user' => ['user'];
        yield 'followTime' => ['followTime'];
    }

    /** @return iterable<string, array{string}> */
    public static function provideEditPageFields(): iterable
    {
        return [
            'wechatType' => ['wechatType'],
            'fansStatus' => ['fansStatus'],
            'unionId' => ['unionId'],
            'user' => ['user'],
            'followTime' => ['followTime'],
        ];
    }

    public function testGetEntityFqcn(): void
    {
        $controller = new WechatInfoCrudController();

        $this->assertSame(WechatInfo::class, $controller::getEntityFqcn());
    }

    public function testConfigureFields(): void
    {
        $controller = new WechatInfoCrudController();
        $fields = iterator_to_array($controller->configureFields('index'));

        $this->assertNotEmpty($fields);
        // 可以添加更多字段验证
    }
}
