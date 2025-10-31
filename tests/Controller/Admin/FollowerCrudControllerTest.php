<?php

declare(strict_types=1);

namespace YouzanApiUserBundle\Tests\Controller\Admin;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\RunTestsInSeparateProcesses;
use Tourze\PHPUnitSymfonyWebTest\AbstractEasyAdminControllerTestCase;
use YouzanApiUserBundle\Controller\Admin\FollowerCrudController;
use YouzanApiUserBundle\Entity\Follower;

/**
 * @internal
 */
#[CoversClass(FollowerCrudController::class)]
#[RunTestsInSeparateProcesses]
final class FollowerCrudControllerTest extends AbstractEasyAdminControllerTestCase
{
    protected function getControllerService(): FollowerCrudController
    {
        return self::getService(FollowerCrudController::class);
    }

    /** @return iterable<string, array{string}> */
    public static function provideIndexPageHeaders(): iterable
    {
        return [
            'ID' => ['ID'],
            '有赞用户ID' => ['有赞用户ID'],
            '微信OpenID' => ['微信OpenID'],
            '昵称' => ['昵称'],
            '省份' => ['省份'],
            '城市' => ['城市'],
            '性别' => ['性别'],
            '是否关注' => ['是否关注'],
            '关联账号' => ['关联账号'],
        ];
    }

    /** @return iterable<string, array{string}> */
    public static function provideNewPageFields(): iterable
    {
        return [
            'userId' => ['userId'],
            'weixinOpenId' => ['weixinOpenId'],
            'nick' => ['nick'],
            'country' => ['country'],
            'province' => ['province'],
            'city' => ['city'],
            'sex' => ['sex'],
            'isFollow' => ['isFollow'],
            'account' => ['account'],
        ];
    }

    /** @return iterable<string, array{string}> */
    public static function provideEditPageFields(): iterable
    {
        return [
            'userId' => ['userId'],
            'weixinOpenId' => ['weixinOpenId'],
            'nick' => ['nick'],
            'country' => ['country'],
            'province' => ['province'],
            'city' => ['city'],
            'sex' => ['sex'],
            'isFollow' => ['isFollow'],
            'account' => ['account'],
        ];
    }

    public function testGetEntityFqcn(): void
    {
        $controller = new FollowerCrudController();

        $this->assertSame(Follower::class, FollowerCrudController::getEntityFqcn());
    }

    public function testConfigureFields(): void
    {
        $controller = new FollowerCrudController();
        $fields = iterator_to_array($controller->configureFields('index'));

        $this->assertNotEmpty($fields);
        // 可以添加更多字段验证
    }
}
