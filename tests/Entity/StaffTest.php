<?php

namespace YouzanApiUserBundle\Tests\Entity;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\RunTestsInSeparateProcesses;
use PHPUnit\Framework\TestCase;
use Tourze\PHPUnitDoctrineEntity\AbstractEntityTestCase;
use YouzanApiUserBundle\Entity\Staff;
use YouzanApiUserBundle\Entity\User;

/**
 * @internal
 */
#[CoversClass(Staff::class)]
final class StaffTest extends AbstractEntityTestCase
{
    protected function createEntity(): object
    {
        return new Staff();
    }

    /**
     * @return iterable<string, array{string, mixed}>
     */
    public static function propertiesProvider(): iterable
    {
        $user = new User();

        return [
            'user' => ['user', $user],
            'corpName' => ['corpName', '有赞科技有限公司'],
            'corpName-null' => ['corpName', null],
            'kdtId' => ['kdtId', 12345678],
            'kdtId-null' => ['kdtId', null],
            'corpId' => ['corpId', 'CORP123456'],
            'corpId-null' => ['corpId', null],
            'email' => ['email', 'test@youzan.com'],
            'email-null' => ['email', null],
            'name' => ['name', '张三'],
            'name-null' => ['name', null],
        ];
    }

    public function testToString(): void
    {
        $staff = new Staff();

        // 当 ID 为 0 时（默认值），应该返回 '[]'
        $this->assertEquals('[]', (string) $staff);

        $staff->setName('李四');
        $staff->setEmail('lisi@youzan.com');
        $this->assertEquals('李四[lisi@youzan.com]', (string) $staff);
    }
}
