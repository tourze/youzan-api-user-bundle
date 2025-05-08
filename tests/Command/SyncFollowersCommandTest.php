<?php

namespace YouzanApiUserBundle\Tests\Command;

use Doctrine\ORM\EntityManagerInterface;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Tester\CommandTester;
use YouzanApiBundle\Entity\Account;
use YouzanApiBundle\Repository\AccountRepository;
use YouzanApiBundle\Service\YouzanClientService;
use YouzanApiUserBundle\Command\SyncFollowersCommand;
use YouzanApiUserBundle\Entity\Follower;
use YouzanApiUserBundle\Enum\GenderEnum;
use YouzanApiUserBundle\Repository\FollowerRepository;

class SyncFollowersCommandTest extends TestCase
{
    private EntityManagerInterface $entityManager;
    private YouzanClientService $clientService;
    private AccountRepository $accountRepository;
    private FollowerRepository $followerRepository;
    private SyncFollowersCommand $command;

    protected function setUp(): void
    {
        $this->entityManager = $this->createMock(EntityManagerInterface::class);
        $this->clientService = $this->createMock(YouzanClientService::class);
        $this->accountRepository = $this->createMock(AccountRepository::class);
        $this->followerRepository = $this->createMock(FollowerRepository::class);

        $this->command = new SyncFollowersCommand(
            $this->entityManager,
            $this->clientService,
            $this->accountRepository,
            $this->followerRepository
        );
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
    public function testExecute_withInvalidAccount_returnsFailure(): void
    {
        // 设置 accountRepository 的模拟行为
        $this->accountRepository->expects($this->once())
            ->method('find')
            ->with(123)
            ->willReturn(null);

        // 创建模拟的输入和输出接口
        $input = $this->createMock(InputInterface::class);
        $output = $this->createMock(OutputInterface::class);

        // 设置输入选项
        $input->expects($this->any())
            ->method('getOption')
            ->willReturnMap([
                ['account', 123],
                ['start-date', '-7 days'],
                ['end-date', 'now']
            ]);

        // 执行命令
        $result = $this->invokeMethod($this->command, 'execute', [$input, $output]);

        // 验证结果
        $this->assertEquals(1, $result);
    }

    /**
     * 测试没有可用账号时的错误处理
     */
    public function testExecute_withNoAccounts_returnsFailure(): void
    {
        // 设置 accountRepository 的模拟行为
        $this->accountRepository->expects($this->once())
            ->method('findAll')
            ->willReturn([]);

        // 创建模拟的输入和输出接口
        $input = $this->createMock(InputInterface::class);
        $output = $this->createMock(OutputInterface::class);

        // 设置输入选项
        $input->expects($this->any())
            ->method('getOption')
            ->willReturnMap([
                ['account', null],
                ['start-date', '-7 days'],
                ['end-date', 'now']
            ]);

        // 执行命令
        $result = $this->invokeMethod($this->command, 'execute', [$input, $output]);

        // 验证结果
        $this->assertEquals(1, $result);
    }

    /**
     * 测试 updateFollower 方法能正确更新粉丝信息
     */
    public function testUpdateFollower_correctlyUpdatesFollowerData(): void
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
    public function testParseGender_convertsStringToEnum(): void
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
    public function testCallApi_correctlyCallsYouzanOpenApi(): void
    {
        // 创建客户端模拟对象
        $client = $this->createMock(\Youzan\Open\Client::class);

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
                    ]
                ]
            ]
        ]);

        // 设置客户端模拟对象的行为
        $client->expects($this->once())
            ->method('post')
            ->with('youzan.users.weixin.followers.info.search', '3.0.0', $params)
            ->willReturn($expectedResponse);

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
     * 辅助方法：调用对象的私有或受保护方法
     */
    private function invokeMethod($object, string $methodName, array $parameters = [])
    {
        $reflection = new \ReflectionClass(get_class($object));
        $method = $reflection->getMethod($methodName);
        $method->setAccessible(true);
        return $method->invokeArgs($object, $parameters);
    }
}
