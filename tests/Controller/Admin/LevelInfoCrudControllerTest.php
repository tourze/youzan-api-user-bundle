<?php

declare(strict_types=1);

namespace YouzanApiUserBundle\Tests\Controller\Admin;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\RunTestsInSeparateProcesses;
use Tourze\PHPUnitSymfonyWebTest\AbstractEasyAdminControllerTestCase;
use YouzanApiUserBundle\Controller\Admin\LevelInfoCrudController;
use YouzanApiUserBundle\Entity\LevelInfo;

/**
 * @internal
 */
#[CoversClass(LevelInfoCrudController::class)]
#[RunTestsInSeparateProcesses]
final class LevelInfoCrudControllerTest extends AbstractEasyAdminControllerTestCase
{
    protected function getControllerService(): LevelInfoCrudController
    {
        return self::getService(LevelInfoCrudController::class);
    }

    /** @return iterable<string, array{string}> */
    public static function provideIndexPageHeaders(): iterable
    {
        return [
            'ID' => ['ID'],
            '会员等级ID' => ['会员等级ID'],
            '会员等级名称' => ['会员等级名称'],
        ];
    }

    /** @return iterable<string, array{string}> */
    public static function provideNewPageFields(): iterable
    {
        return [
            'levelId' => ['levelId'],
            'levelName' => ['levelName'],
        ];
    }

    /** @return iterable<string, array{string}> */
    public static function provideEditPageFields(): iterable
    {
        return [
            'levelId' => ['levelId'],
            'levelName' => ['levelName'],
        ];
    }

    public function testGetEntityFqcn(): void
    {
        $controller = new LevelInfoCrudController();

        $this->assertSame(LevelInfo::class, $controller::getEntityFqcn());
    }

    public function testConfigureFields(): void
    {
        $controller = new LevelInfoCrudController();
        $fields = iterator_to_array($controller->configureFields('index'));

        $this->assertNotEmpty($fields);
        // 可以添加更多字段验证
    }
}
