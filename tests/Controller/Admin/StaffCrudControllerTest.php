<?php

declare(strict_types=1);

namespace YouzanApiUserBundle\Tests\Controller\Admin;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\RunTestsInSeparateProcesses;
use Tourze\PHPUnitSymfonyWebTest\AbstractEasyAdminControllerTestCase;
use YouzanApiUserBundle\Controller\Admin\StaffCrudController;
use YouzanApiUserBundle\Entity\Staff;

/**
 * @internal
 */
#[CoversClass(StaffCrudController::class)]
#[RunTestsInSeparateProcesses]
final class StaffCrudControllerTest extends AbstractEasyAdminControllerTestCase
{
    protected function getControllerService(): StaffCrudController
    {
        return self::getService(StaffCrudController::class);
    }

    /** @return iterable<string, array{string}> */
    public static function provideIndexPageHeaders(): iterable
    {
        return [
            'ID' => ['ID'],
            '企业名称' => ['企业名称'],
            '员工邮箱' => ['员工邮箱'],
            '员工名称' => ['员工名称'],
            '关联用户' => ['关联用户'],
        ];
    }

    /** @return iterable<string, array{string}> */
    public static function provideNewPageFields(): iterable
    {
        yield 'corpName' => ['corpName'];
        yield 'email' => ['email'];
        yield 'name' => ['name'];
        yield 'user' => ['user'];
    }

    /** @return iterable<string, array{string}> */
    public static function provideEditPageFields(): iterable
    {
        return [
            'corpName' => ['corpName'],
            'email' => ['email'],
            'name' => ['name'],
            'user' => ['user'],
        ];
    }

    public function testGetEntityFqcn(): void
    {
        $controller = new StaffCrudController();

        $this->assertSame(Staff::class, StaffCrudController::getEntityFqcn());
    }

    public function testConfigureFields(): void
    {
        $controller = new StaffCrudController();
        $fields = iterator_to_array($controller->configureFields('index'));

        $this->assertNotEmpty($fields);
        // 可以添加更多字段验证
    }
}
