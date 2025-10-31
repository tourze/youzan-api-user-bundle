<?php

declare(strict_types=1);

namespace YouzanApiUserBundle\Controller\Admin;

use EasyCorp\Bundle\EasyAdminBundle\Attribute\AdminCrud;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Config\Filters;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use Tourze\EasyAdminEnumFieldBundle\Field\EnumField;
use YouzanApiUserBundle\Entity\WechatInfo;
use YouzanApiUserBundle\Enum\FansStatusEnum;
use YouzanApiUserBundle\Enum\WechatTypeEnum;

#[AdminCrud(routePath: '/youzan-api-user/wechat-info', routeName: 'youzan_api_user_wechat_info')]
final class WechatInfoCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return WechatInfo::class;
    }

    public function configureFields(string $pageName): iterable
    {
        yield IdField::new('id', 'ID')->onlyOnIndex();

        $wechatTypeField = EnumField::new('wechatType', '微信类型');
        $wechatTypeField->setEnumCases(WechatTypeEnum::cases());
        $wechatTypeField->setHelp('微信平台类型（公众号/小程序）');
        yield $wechatTypeField;

        $fansStatusField = EnumField::new('fansStatus', '粉丝状态');
        $fansStatusField->setEnumCases(FansStatusEnum::cases());
        $fansStatusField->setHelp('用户的关注状态');
        yield $fansStatusField;

        yield TextField::new('unionId', '微信UnionID')
            ->setHelp('微信开放平台UnionID')
            ->hideOnIndex()
        ;

        yield DateTimeField::new('followTime', '关注时间')
            ->setHelp('用户首次关注的时间')
            ->hideOnIndex()
        ;

        yield DateTimeField::new('lastTalkTime', '最后交谈时间')
            ->setHelp('用户最后一次与公众号交互的时间')
            ->hideOnIndex()
        ;

        yield DateTimeField::new('unfollowTime', '取消关注时间')
            ->setHelp('用户取消关注的时间')
            ->hideOnIndex()
        ;

        yield AssociationField::new('user', '关联用户')
            ->setHelp('微信信息关联的有赞用户')
        ;

        yield DateTimeField::new('createTime', '创建时间')
            ->onlyOnDetail()
        ;

        yield DateTimeField::new('updateTime', '更新时间')
            ->onlyOnDetail()
        ;
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setEntityLabelInSingular('微信信息')
            ->setEntityLabelInPlural('微信信息')
            ->setDefaultSort(['id' => 'DESC'])
            ->setPaginatorPageSize(20)
        ;
    }

    public function configureFilters(Filters $filters): Filters
    {
        return $filters
            ->add('wechatType')
            ->add('fansStatus')
            ->add('unionId')
            ->add('followTime')
            ->add('unfollowTime')
            ->add('user')
        ;
    }
}
