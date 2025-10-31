<?php

declare(strict_types=1);

namespace YouzanApiUserBundle\Tests\Controller\Admin;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\RunTestsInSeparateProcesses;
use Tourze\PHPUnitSymfonyWebTest\AbstractEasyAdminControllerTestCase;
use YouzanApiUserBundle\Controller\Admin\UserCrudController;
use YouzanApiUserBundle\Entity\User;

/**
 * @internal
 */
#[CoversClass(UserCrudController::class)]
#[RunTestsInSeparateProcesses]
final class UserCrudControllerTest extends AbstractEasyAdminControllerTestCase
{
    public function testGetEntityFqcn(): void
    {
        $controller = new UserCrudController();

        $this->assertSame(User::class, UserCrudController::getEntityFqcn());
    }

    public function testConfigureFields(): void
    {
        $controller = new UserCrudController();
        $fields = iterator_to_array($controller->configureFields('index'));

        $this->assertNotEmpty($fields);
        // 可以添加更多字段验证
    }

    protected function getControllerService(): UserCrudController
    {
        return self::getService(UserCrudController::class);
    }

    /** @return iterable<string, array{string}> */
    public static function provideIndexPageHeaders(): iterable
    {
        return [
            'ID' => ['ID'],
            '有赞用户ID' => ['有赞用户ID'],
            '头像' => ['头像'],
            '国家' => ['国家'],
            '省份' => ['省份'],
            '城市' => ['城市'],
            '性别' => ['性别'],
            '平台类型' => ['平台类型'],
            '关联账号' => ['关联账号'],
        ];
    }

    /** @return iterable<string, array{string}> */
    public static function provideNewPageFields(): iterable
    {
        return [
            'yzOpenId' => ['yzOpenId'],
            'nickNameDecrypted' => ['nickNameDecrypted'],
            'avatar' => ['avatar'],
            'country' => ['country'],
            'province' => ['province'],
            'city' => ['city'],
            'gender' => ['gender'],
            'platformType' => ['platformType'],
            'account' => ['account'],
        ];
    }

    /** @return iterable<string, array{string}> */
    public static function provideEditPageFields(): iterable
    {
        return [
            'yzOpenId' => ['yzOpenId'],
            'nickNameDecrypted' => ['nickNameDecrypted'],
            'avatar' => ['avatar'],
            'country' => ['country'],
            'province' => ['province'],
            'city' => ['city'],
            'gender' => ['gender'],
            'platformType' => ['platformType'],
            'account' => ['account'],
        ];
    }
}
