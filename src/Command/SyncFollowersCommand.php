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
use Youzan\Open\Client;
use YouzanApiBundle\Entity\Account;
use YouzanApiBundle\Repository\AccountRepository;
use YouzanApiBundle\Service\YouzanClientService;
use YouzanApiUserBundle\Entity\Follower;
use YouzanApiUserBundle\Enum\GenderEnum;
use YouzanApiUserBundle\Exception\YouzanApiException;
use YouzanApiUserBundle\Repository\FollowerRepository;

#[AsCommand(
    name: self::NAME,
    description: '同步有赞微信粉丝信息',
)]
class SyncFollowersCommand extends Command
{
    public const NAME = 'youzan:sync:followers';

    private const BATCH_SIZE = 100;
    private const PAGE_SIZE = 50;

    public function __construct(
        private readonly EntityManagerInterface $entityManager,
        private readonly YouzanClientService $clientService,
        private readonly AccountRepository $accountRepository,
        private readonly FollowerRepository $followerRepository,
    ) {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this->addOption('account', 'a', InputOption::VALUE_OPTIONAL, '账号ID')
            ->addOption('start-date', 's', InputOption::VALUE_OPTIONAL, '开始日期 (Y-m-d)', '-7 days')
            ->addOption('end-date', 'e', InputOption::VALUE_OPTIONAL, '结束日期 (Y-m-d)', 'now')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        $accounts = $this->getAccountsToProcess($input, $io);
        if (null === $accounts) {
            return Command::FAILURE;
        }

        $dateRange = $this->getDateRange($input);
        $totalProcessed = $this->processAllAccounts($accounts, $dateRange, $output, $io);

        $io->success(sprintf('所有账号处理完成，共同步 %d 个粉丝信息', $totalProcessed));

        return Command::SUCCESS;
    }

    private function syncFollowers(
        Account $account,
        \DateTime $startDate,
        \DateTime $endDate,
        OutputInterface $output,
    ): int {
        $client = $this->clientService->getClient($account);
        $apiParams = $this->buildApiParams($startDate, $endDate);

        $response = $this->callApi($client, $apiParams);
        $this->validateApiResponse($response);

        $total = $response['data']['total_results'];
        $pages = (int) ceil($total / self::PAGE_SIZE);

        $progressBar = $this->createProgressBar($output, $total);

        return $this->processAllPages($client, $apiParams, $pages, $account, $progressBar, $output);
    }

    /**
     * @param array<string, mixed> $data
     */
    private function updateFollower(Follower $follower, array $data, Account $account): void
    {
        $follower->setUserId($data['user_id']);
        $follower->setWeixinOpenId($data['weixin_open_id']);
        $follower->setNick($data['nick']);
        $follower->setAvatar($data['avatar']);
        $follower->setCountry($data['country']);
        $follower->setProvince($data['province']);
        $follower->setCity($data['city']);
        $follower->setSex($this->parseGender($data['sex']));
        $follower->setIsFollow($data['is_follow']);
        $follower->setFollowTime(null !== $data['follow_time'] && '' !== $data['follow_time'] ? (int) $data['follow_time'] : null);
        $follower->setTradedNum($data['traded_num']);
        $follower->setTradeMoney($data['trade_money']);
        $follower->setPoints($data['points']);
        $follower->setAccount($account);
    }

    private function parseGender(string $sex): GenderEnum
    {
        return match ($sex) {
            'm' => GenderEnum::MALE,
            'f' => GenderEnum::FEMALE,
            default => GenderEnum::UNKNOWN,
        };
    }

    /**
     * @return array<Account>|null
     */
    private function getAccountsToProcess(InputInterface $input, SymfonyStyle $io): ?array
    {
        $accountId = $input->getOption('account');

        if (null !== $accountId) {
            return $this->getSingleAccount($accountId, $io);
        }

        return $this->getAllAccounts($io);
    }

    /**
     * @return array<Account>|null
     */
    private function getSingleAccount(string $accountId, SymfonyStyle $io): ?array
    {
        $account = $this->accountRepository->find($accountId);
        if (!$account instanceof Account) {
            $io->error('账号不存在');

            return null;
        }

        return [$account];
    }

    /**
     * @return array<Account>|null
     */
    private function getAllAccounts(SymfonyStyle $io): ?array
    {
        $accounts = $this->accountRepository->findAll();
        if (0 === count($accounts)) {
            $io->error('没有可用的账号');

            return null;
        }

        return $accounts;
    }

    /**
     * @return array<string, \DateTime>
     */
    private function getDateRange(InputInterface $input): array
    {
        return [
            'start' => new \DateTime($input->getOption('start-date')),
            'end' => new \DateTime($input->getOption('end-date')),
        ];
    }

    /**
     * @param array<Account> $accounts
     * @param array<string, \DateTime> $dateRange
     */
    private function processAllAccounts(array $accounts, array $dateRange, OutputInterface $output, SymfonyStyle $io): int
    {
        $totalProcessed = 0;
        $io->section('开始处理账号列表');

        foreach ($accounts as $account) {
            $processed = $this->processAccount($account, $dateRange, $output, $io);
            $totalProcessed += $processed;
            $this->entityManager->clear();
        }

        return $totalProcessed;
    }

    /**
     * @param array<string, \DateTime> $dateRange
     */
    private function processAccount(Account $account, array $dateRange, OutputInterface $output, SymfonyStyle $io): int
    {
        $io->section(sprintf('正在处理账号: %s', $account->getName()));

        try {
            $processed = $this->syncFollowers($account, $dateRange['start'], $dateRange['end'], $output);
            $io->success(sprintf('成功同步 %d 个粉丝信息', $processed));

            return $processed;
        } catch (\Throwable $e) {
            $io->error(sprintf('账号 %s 同步失败: %s', $account->getName(), $e->getMessage()));

            return 0;
        }
    }

    /**
     * @return array<string, mixed>
     */
    private function buildApiParams(\DateTime $startDate, \DateTime $endDate, int $pageNo = 1): array
    {
        return [
            'start_follow' => $startDate->format('Y-m-d H:i:s'),
            'end_follow' => $endDate->format('Y-m-d H:i:s'),
            'only_follow' => '0',
            'page_no' => $pageNo,
            'page_size' => self::PAGE_SIZE,
            'fields' => 'trade,points',
        ];
    }

    /**
     * @param array<string, mixed> $response
     */
    private function validateApiResponse(array $response): void
    {
        if (true !== ($response['success'] ?? false)) {
            throw new YouzanApiException($response['message'] ?? '接口调用失败');
        }
    }

    private function createProgressBar(OutputInterface $output, int $total): ProgressBar
    {
        $progressBar = new ProgressBar($output, $total);
        $progressBar->setFormat('debug');
        $progressBar->start();

        return $progressBar;
    }

    /**
     * @param array<string, mixed> $baseApiParams
     */
    private function processAllPages(
        Client $client,
        array $baseApiParams,
        int $pages,
        Account $account,
        ProgressBar $progressBar,
        OutputInterface $output,
    ): int {
        $processed = 0;
        $batchCount = 0;

        for ($page = 1; $page <= $pages; ++$page) {
            $apiParams = array_merge($baseApiParams, ['page_no' => $page]);
            $response = $this->callApi($client, $apiParams);

            foreach ($response['data']['users'] as $userData) {
                $processed += $this->processFollowerData($userData, $account, $progressBar);
                $batchCount = $this->handleBatchCommit($batchCount);
            }
        }

        $this->finalizeProcessing($batchCount, $progressBar, $output);

        return $processed;
    }

    /**
     * @param array<string, mixed> $userData
     */
    private function processFollowerData(array $userData, Account $account, ProgressBar $progressBar): int
    {
        $follower = $this->followerRepository->findByUserId($userData['user_id']) ?? new Follower();
        $this->updateFollower($follower, $userData, $account);
        $this->entityManager->persist($follower);
        $progressBar->advance();

        return 1;
    }

    private function handleBatchCommit(int $batchCount): int
    {
        ++$batchCount;

        if ($batchCount >= self::BATCH_SIZE) {
            $this->entityManager->flush();
            $this->entityManager->clear();

            return 0;
        }

        return $batchCount;
    }

    private function finalizeProcessing(int $batchCount, ProgressBar $progressBar, OutputInterface $output): void
    {
        if ($batchCount > 0) {
            $this->entityManager->flush();
        }

        $progressBar->finish();
        $output->writeln('');
    }

    /**
     * @param array<string, mixed> $params
     * @return array<string, mixed>
     */
    private function callApi(Client $client, array $params): array
    {
        $response = $client->post('youzan.users.weixin.followers.info.search', '3.0.0', $params);

        return json_decode($response, true, 512, JSON_THROW_ON_ERROR);
    }
}
