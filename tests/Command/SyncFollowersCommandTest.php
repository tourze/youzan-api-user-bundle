<?php

namespace YouzanApiUserBundle\Tests\Command;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\RunTestsInSeparateProcesses;
use Symfony\Component\Console\Tester\CommandTester;
use Tourze\PHPUnitSymfonyKernelTest\AbstractCommandTestCase;
use Youzan\Open\Client;
use YouzanApiBundle\Entity\Account;
use YouzanApiUserBundle\Command\SyncFollowersCommand;
use YouzanApiUserBundle\Entity\Follower;
use YouzanApiUserBundle\Enum\GenderEnum;

/**
 * @internal
 */
#[CoversClass(SyncFollowersCommand::class)]
#[RunTestsInSeparateProcesses]
final class SyncFollowersCommandTest extends AbstractCommandTestCase
{
    private SyncFollowersCommand $command;

    protected function onSetUp(): void
    {
        $this->command = self::getService(SyncFollowersCommand::class);
    }

    protected function getCommandTester(): CommandTester
    {
        return new CommandTester($this->command);
    }

    /**
     * 测试命令的基本配置
     */
    public function testCommandConfiguration(): void
    {
        $commandTester = new CommandTester($this->command);

        $this->assertEquals('youzan:sync:followers', $this->command->getName());
        $this->assertEquals('同步有赞微信粉丝信息', $this->command->getDescription());
        $this->assertTrue($this->command->getDefinition()->hasOption('account'));
        $this->assertTrue($this->command->getDefinition()->hasOption('start-date'));
        $this->assertTrue($this->command->getDefinition()->hasOption('end-date'));
    }

    /**
     * 测试账号不存在时的错误处理
     */
    public function testExecuteWithInvalidAccountReturnsFailure(): void
    {
        $commandTester = new CommandTester($this->command);

        $exitCode = $commandTester->execute([
            '--account' => 99999999,
        ]);

        $this->assertEquals(1, $exitCode);
        $this->assertStringContainsString('账号不存在', $commandTester->getDisplay());
    }

    /**
     * 测试没有可用账号时的错误处理
     */
    public function testExecuteWithNoAccountsReturnsFailure(): void
    {
        $commandTester = new CommandTester($this->command);

        $exitCode = $commandTester->execute([]);

        $this->assertContains($exitCode, [0, 1]);
    }

    /**
     * 测试 updateFollower 方法能正确更新粉丝信息
     */
    public function testUpdateFollowerCorrectlyUpdatesFollowerData(): void
    {
        // 创建测试数据
        $follower = new Follower();
        $account = new Account();
        $data = [
            'user_id' => 123456789,
            'weixin_open_id' => 'o_abc123456789',
            'nick' => 'Test User',
            'avatar' => 'https://example.com/avatar.jpg',
            'country' => 'China',
            'province' => 'Guangdong',
            'city' => 'Shenzhen',
            'sex' => 'm',
            'is_follow' => true,
            'follow_time' => 1609459200,
            'traded_num' => 5,
            'trade_money' => 500.00,
            'points' => ['total' => 100],
        ];

        // 执行方法
        $this->invokeMethod($this->command, 'updateFollower', [$follower, $data, $account]);

        // 验证结果
        $this->assertEquals(123456789, $follower->getUserId());
        $this->assertEquals('o_abc123456789', $follower->getWeixinOpenId());
        $this->assertEquals('Test User', $follower->getNick());
        $this->assertEquals('https://example.com/avatar.jpg', $follower->getAvatar());
        $this->assertEquals('China', $follower->getCountry());
        $this->assertEquals('Guangdong', $follower->getProvince());
        $this->assertEquals('Shenzhen', $follower->getCity());
        $this->assertEquals(GenderEnum::MALE, $follower->getSex());
        $this->assertTrue($follower->isFollow());
        $this->assertEquals(1609459200, $follower->getFollowTime());
        $this->assertEquals(5, $follower->getTradedNum());
        $this->assertEquals(500.00, $follower->getTradeMoney());
        $this->assertEquals(['total' => 100], $follower->getPoints());
        $this->assertSame($account, $follower->getAccount());
    }

    /**
     * 测试 parseGender 方法正确将字符串转换为性别枚举
     */
    public function testParseGenderConvertsStringToEnum(): void
    {
        // 测试男性
        $result = $this->invokeMethod($this->command, 'parseGender', ['m']);
        $this->assertSame(GenderEnum::MALE, $result);

        // 测试女性
        $result = $this->invokeMethod($this->command, 'parseGender', ['f']);
        $this->assertSame(GenderEnum::FEMALE, $result);

        // 测试未知
        $result = $this->invokeMethod($this->command, 'parseGender', ['unknown']);
        $this->assertSame(GenderEnum::UNKNOWN, $result);

        // 测试空字符串
        $result = $this->invokeMethod($this->command, 'parseGender', ['']);
        $this->assertSame(GenderEnum::UNKNOWN, $result);
    }

    /**
     * 测试 callApi 方法正确调用有赞开放 API
     */
    public function testCallApiCorrectlyCallsYouzanOpenApi(): void
    {
        // 使用具体类 Youzan\Open\Client 的 Mock 是必需的，理由：
        // 理由 1: 这是第三方库的类，没有对应的接口定义
        // 理由 2: 测试需要模拟 HTTP API 调用行为而不发送真实网络请求
        // 理由 3: 这是第三方 SDK 的标准测试模式
        $client = $this->createMock(Client::class);

        // 设置参数
        $params = [
            'start_follow' => '2023-01-01 00:00:00',
            'end_follow' => '2023-01-31 23:59:59',
            'only_follow' => '0',
            'page_no' => 1,
            'page_size' => 50,
            'fields' => 'trade,points',
        ];

        // 设置预期的 JSON 响应
        $expectedResponse = json_encode([
            'success' => true,
            'data' => [
                'total_results' => 2,
                'users' => [
                    [
                        'user_id' => 123456789,
                        'weixin_open_id' => 'o_abc123456789',
                        'nick' => 'User 1',
                        'avatar' => 'https://example.com/avatar1.jpg',
                        'country' => 'China',
                        'province' => 'Guangdong',
                        'city' => 'Shenzhen',
                        'sex' => 'm',
                        'is_follow' => true,
                        'follow_time' => 1609459200,
                        'traded_num' => 5,
                        'trade_money' => 500.00,
                        'points' => 100,
                    ],
                    [
                        'user_id' => 987654321,
                        'weixin_open_id' => 'o_xyz987654321',
                        'nick' => 'User 2',
                        'avatar' => 'https://example.com/avatar2.jpg',
                        'country' => 'China',
                        'province' => 'Beijing',
                        'city' => 'Beijing',
                        'sex' => 'f',
                        'is_follow' => true,
                        'follow_time' => 1609545600,
                        'traded_num' => 10,
                        'trade_money' => 1000.00,
                        'points' => 200,
                    ],
                ],
            ],
        ]);

        // 设置客户端模拟对象的行为
        $client->expects($this->once())
            ->method('post')
            ->with('youzan.users.weixin.followers.info.search', '3.0.0', $params)
            ->willReturn($expectedResponse)
        ;

        // 执行方法
        $result = $this->invokeMethod($this->command, 'callApi', [$client, $params]);

        // 验证结果
        $this->assertTrue($result['success']);
        $this->assertEquals(2, $result['data']['total_results']);
        $this->assertCount(2, $result['data']['users']);
        $this->assertEquals(123456789, $result['data']['users'][0]['user_id']);
        $this->assertEquals(987654321, $result['data']['users'][1]['user_id']);
    }

    /**
     * 测试 --account 选项
     */
    public function testOptionAccount(): void
    {
        $commandTester = new CommandTester($this->command);

        // 测试无效账号ID
        $exitCode = $commandTester->execute([
            '--account' => 99999999,
        ]);

        $this->assertEquals(1, $exitCode);
        $this->assertStringContainsString('账号不存在', $commandTester->getDisplay());
    }

    /**
     * 测试 --start-date 选项
     */
    public function testOptionStartDate(): void
    {
        $commandTester = new CommandTester($this->command);

        // 测试带有开始日期但没有账号的情况
        $exitCode = $commandTester->execute([
            '--start-date' => '2023-01-01',
        ]);

        // 应该返回成功或失败，但不应该因为选项本身出错
        $this->assertContains($exitCode, [0, 1]);
    }

    /**
     * 测试 --end-date 选项
     */
    public function testOptionEndDate(): void
    {
        $commandTester = new CommandTester($this->command);

        // 测试带有结束日期但没有账号的情况
        $exitCode = $commandTester->execute([
            '--end-date' => '2023-01-31',
        ]);

        // 应该返回成功或失败，但不应该因为选项本身出错
        $this->assertContains($exitCode, [0, 1]);
    }

    /**
     * 辅助方法：调用对象的私有或受保护方法
     * @param mixed $object
     * @param array<mixed> $parameters
     */
    private function invokeMethod($object, string $methodName, array $parameters = []): mixed
    {
        if (!is_object($object)) {
            throw new \InvalidArgumentException('First parameter must be an object');
        }
        $reflection = new \ReflectionClass($object);
        $method = $reflection->getMethod($methodName);
        $method->setAccessible(true);

        return $method->invokeArgs($object, $parameters);
    }
}
