<?php

declare(strict_types=1);

namespace YouzanApiUserBundle\Service;

use Knp\Menu\ItemInterface;
use Symfony\Component\DependencyInjection\Attribute\Autoconfigure;
use Tourze\EasyAdminMenuBundle\Service\LinkGeneratorInterface;
use Tourze\EasyAdminMenuBundle\Service\MenuProviderInterface;
use YouzanApiUserBundle\Entity\Follower;
use YouzanApiUserBundle\Entity\LevelInfo;
use YouzanApiUserBundle\Entity\MobileInfo;
use YouzanApiUserBundle\Entity\Staff;
use YouzanApiUserBundle\Entity\User;
use YouzanApiUserBundle\Entity\WechatInfo;

/**
 * 有赞用户管理后台菜单提供者
 */
#[Autoconfigure(public: true)]
readonly class AdminMenu implements MenuProviderInterface
{
    public function __construct(
        private LinkGeneratorInterface $linkGenerator,
    ) {
    }

    public function __invoke(ItemInterface $item): void
    {
        // 创建有赞用户管理主菜单
        if (null === $item->getChild('有赞用户管理')) {
            $item->addChild('有赞用户管理')
                ->setAttribute('icon', 'fas fa-users')
            ;
        }

        $youzanMenu = $item->getChild('有赞用户管理');
        if (null === $youzanMenu) {
            return;
        }

        // 核心用户管理子菜单
        $youzanMenu->addChild('有赞用户')
            ->setUri($this->linkGenerator->getCurdListPage(User::class))
            ->setAttribute('icon', 'fas fa-user')
        ;

        $youzanMenu->addChild('粉丝管理')
            ->setUri($this->linkGenerator->getCurdListPage(Follower::class))
            ->setAttribute('icon', 'fas fa-heart')
        ;

        // 用户详细信息子菜单组
        if (null === $youzanMenu->getChild('用户详细信息')) {
            $youzanMenu->addChild('用户详细信息')
                ->setAttribute('icon', 'fas fa-address-card')
            ;
        }

        $detailMenu = $youzanMenu->getChild('用户详细信息');
        if (null !== $detailMenu) {
            $detailMenu->addChild('员工信息')
                ->setUri($this->linkGenerator->getCurdListPage(Staff::class))
                ->setAttribute('icon', 'fas fa-id-card')
            ;

            $detailMenu->addChild('微信信息')
                ->setUri($this->linkGenerator->getCurdListPage(WechatInfo::class))
                ->setAttribute('icon', 'fab fa-weixin')
            ;

            $detailMenu->addChild('手机信息')
                ->setUri($this->linkGenerator->getCurdListPage(MobileInfo::class))
                ->setAttribute('icon', 'fas fa-mobile-alt')
            ;
        }

        // 系统配置子菜单
        if (null === $youzanMenu->getChild('系统配置')) {
            $youzanMenu->addChild('系统配置')
                ->setAttribute('icon', 'fas fa-cog')
            ;
        }

        $configMenu = $youzanMenu->getChild('系统配置');
        if (null !== $configMenu) {
            $configMenu->addChild('会员等级')
                ->setUri($this->linkGenerator->getCurdListPage(LevelInfo::class))
                ->setAttribute('icon', 'fas fa-star')
            ;
        }
    }
}
