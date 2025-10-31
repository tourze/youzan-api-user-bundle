<?php

declare(strict_types=1);

namespace YouzanApiUserBundle\Tests\Controller\Admin;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\RunTestsInSeparateProcesses;
use Tourze\PHPUnitSymfonyWebTest\AbstractEasyAdminControllerTestCase;
use YouzanApiUserBundle\Controller\Admin\MobileInfoCrudController;
use YouzanApiUserBundle\Entity\MobileInfo;

/**
 * @internal
 */
#[CoversClass(MobileInfoCrudController::class)]
#[RunTestsInSeparateProcesses]
final class MobileInfoCrudControllerTest extends AbstractEasyAdminControllerTestCase
{
    protected function getControllerService(): MobileInfoCrudController
    {
        return self::getService(MobileInfoCrudController::class);
    }

    /** @return iterable<string, array{string}> */
    public static function provideIndexPageHeaders(): iterable
    {
        return [
            'ID' => ['ID'],
            '国家代码' => ['国家代码'],
            '关联用户' => ['关联用户'],
        ];
    }

    /** @return iterable<string, array{string}> */
    public static function provideNewPageFields(): iterable
    {
        yield 'countryCode' => ['countryCode'];
        yield 'mobileDecrypted' => ['mobileDecrypted'];
        yield 'user' => ['user'];
    }

    /** @return iterable<string, array{string}> */
    public static function provideEditPageFields(): iterable
    {
        return [
            'countryCode' => ['countryCode'],
            'mobileDecrypted' => ['mobileDecrypted'],
            'user' => ['user'],
        ];
    }

    public function testGetEntityFqcn(): void
    {
        $controller = new MobileInfoCrudController();

        $this->assertSame(MobileInfo::class, $controller::getEntityFqcn());
    }

    public function testConfigureFields(): void
    {
        $controller = new MobileInfoCrudController();
        $fields = iterator_to_array($controller->configureFields('index'));

        $this->assertNotEmpty($fields);
        // 可以添加更多字段验证
    }
}
