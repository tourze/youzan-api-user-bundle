<?php

namespace YouzanApiUserBundle\Command;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Helper\ProgressBar;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use YouzanApiBundle\Entity\Account;
use YouzanApiBundle\Repository\AccountRepository;
use YouzanApiBundle\Service\YouzanClientService;
use YouzanApiUserBundle\Entity\Follower;
use YouzanApiUserBundle\Enum\GenderEnum;
use YouzanApiUserBundle\Repository\FollowerRepository;

#[AsCommand(
    name: 'youzan:sync:followers',
    description: '同步有赞微信粉丝信息',
)]
class SyncFollowersCommand extends Command
{
    private const BATCH_SIZE = 100;
    private const PAGE_SIZE = 50;

    public function __construct(
        private readonly EntityManagerInterface $entityManager,
        private readonly YouzanClientService    $clientService,
        private readonly AccountRepository      $accountRepository,
        private readonly FollowerRepository     $followerRepository,
    ) {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this->addOption('account', 'a', InputOption::VALUE_OPTIONAL, '账号ID')
            ->addOption('start-date', 's', InputOption::VALUE_OPTIONAL, '开始日期 (Y-m-d)', '-7 days')
            ->addOption('end-date', 'e', InputOption::VALUE_OPTIONAL, '结束日期 (Y-m-d)', 'now');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        // 获取账号列表
        $accounts = [];
        if ($accountId = $input->getOption('account')) {
            $account = $this->accountRepository->find($accountId);
            if (!$account instanceof Account) {
                $io->error('账号不存在');
                return Command::FAILURE;
            }
            $accounts = [$account];
        } else {
            $accounts = $this->accountRepository->findAll();
            if (empty($accounts)) {
                $io->error('没有可用的账号');
                return Command::FAILURE;
            }
        }

        // 获取日期范围
        $startDate = new \DateTime($input->getOption('start-date'));
        $endDate = new \DateTime($input->getOption('end-date'));

        $totalProcessed = 0;
        $io->section('开始处理账号列表');
        $accountProgressBar = new ProgressBar($output, count($accounts));
        $accountProgressBar->setFormat('debug');
        $accountProgressBar->start();
        foreach ($accounts as $account) {
            $io->section(sprintf('正在处理账号: %s', $account->getName()));

            try {
                $processed = $this->syncFollowers($account, $startDate, $endDate, $output);
                $totalProcessed += $processed;

                $io->success(sprintf('成功同步 %d 个粉丝信息', $processed));
            } catch (\Throwable $e) {
                $io->error(sprintf('账号 %s 同步失败: %s', $account->getName(), $e->getMessage()));
            }

            // 清理内存
            $this->entityManager->clear();
            $accountProgressBar->advance();
        }
        $accountProgressBar->finish();
        $output->writeln('');

        $io->success(sprintf('所有账号处理完成，共同步 %d 个粉丝信息', $totalProcessed));
        return Command::SUCCESS;
    }

    private function syncFollowers(
        Account         $account,
        \DateTime       $startDate,
        \DateTime       $endDate,
        OutputInterface $output
    ): int {
        // 获取客户端
        $client = $this->clientService->getClient($account);

        // 第一次调用 API 获取总数
        $response = $this->callApi($client, [
            'start_follow' => $startDate->format('Y-m-d H:i:s'),
            'end_follow' => $endDate->format('Y-m-d H:i:s'),
            'only_follow' => '0',
            'page_no' => 1,
            'page_size' => self::PAGE_SIZE,
            'fields' => 'trade,points',
        ]);

        if (!$response['success']) {
            throw new \RuntimeException($response['message'] ?? '接口调用失败');
        }

        $total = $response['data']['total_results'];
        $pages = ceil($total / self::PAGE_SIZE);

        // 创建进度条
        $progressBar = new ProgressBar($output, $total);
        $progressBar->setFormat('debug');
        $progressBar->start();

        $processed = 0;
        $batchCount = 0;

        // 分页处理
        for ($page = 1; $page <= $pages; $page++) {
            if ($page > 1) {
                $response = $this->callApi($client, [
                    'start_follow' => $startDate->format('Y-m-d H:i:s'),
                    'end_follow' => $endDate->format('Y-m-d H:i:s'),
                    'only_follow' => '0',
                    'page_no' => $page,
                    'page_size' => self::PAGE_SIZE,
                    'fields' => 'trade,points',
                ]);
            }

            foreach ($response['data']['users'] as $userData) {
                // 查找或创建粉丝
                $follower = $this->followerRepository->findByUserId($userData['user_id'])
                    ?? new Follower();

                // 更新粉丝信息
                $this->updateFollower($follower, $userData, $account);

                // 持久化
                $this->entityManager->persist($follower);

                $processed++;
                $batchCount++;
                $progressBar->advance();

                // 批量提交
                if ($batchCount >= self::BATCH_SIZE) {
                    $this->entityManager->flush();
                    $this->entityManager->clear(Follower::class);
                    $batchCount = 0;
                }
            }
        }

        // 提交剩余的数据
        if ($batchCount > 0) {
            $this->entityManager->flush();
        }

        $progressBar->finish();
        $output->writeln('');

        return $processed;
    }

    private function updateFollower(Follower $follower, array $data, Account $account): void
    {
        $follower->setUserId($data['user_id'])
            ->setWeixinOpenId($data['weixin_open_id'])
            ->setNick($data['nick'])
            ->setAvatar($data['avatar'])
            ->setCountry($data['country'])
            ->setProvince($data['province'])
            ->setCity($data['city'])
            ->setSex($this->parseGender($data['sex']))
            ->setIsFollow($data['is_follow'])
            ->setFollowTime($data['follow_time'] ? (int)$data['follow_time'] : null)
            ->setTradedNum($data['traded_num'])
            ->setTradeMoney($data['trade_money'])
            ->setPoints($data['points'])
            ->setAccount($account);
    }

    private function parseGender(string $sex): GenderEnum
    {
        return match ($sex) {
            'm' => GenderEnum::MALE,
            'f' => GenderEnum::FEMALE,
            default => GenderEnum::UNKNOWN,
        };
    }

    private function callApi(\Youzan\Open\Client $client, array $params): array
    {
        $response = $client->post('youzan.users.weixin.followers.info.search', '3.0.0', $params);
        return json_decode($response, true, 512, JSON_THROW_ON_ERROR);
    }
}
