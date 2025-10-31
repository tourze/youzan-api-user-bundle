<?php

declare(strict_types=1);

namespace YouzanApiUserBundle\Controller\Admin;

use EasyCorp\Bundle\EasyAdminBundle\Attribute\AdminCrud;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Config\Filters;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\BooleanField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IntegerField;
use EasyCorp\Bundle\EasyAdminBundle\Field\MoneyField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextareaField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Field\UrlField;
use Tourze\EasyAdminEnumFieldBundle\Field\EnumField;
use YouzanApiUserBundle\Entity\Follower;
use YouzanApiUserBundle\Enum\GenderEnum;

#[AdminCrud(routePath: '/youzan-api-user/follower', routeName: 'youzan_api_user_follower')]
final class FollowerCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Follower::class;
    }

    public function configureFields(string $pageName): iterable
    {
        yield IdField::new('id', 'ID')->onlyOnIndex();

        yield IntegerField::new('userId', '有赞用户ID')
            ->setHelp('用户在有赞平台的数字ID')
        ;

        yield TextField::new('weixinOpenId', '微信OpenID')
            ->setHelp('用户的微信OpenID标识')
        ;

        yield TextField::new('nick', '昵称')
            ->setHelp('用户昵称')
        ;

        yield UrlField::new('avatar', '头像')
            ->setHelp('用户头像链接地址')
            ->hideOnIndex()
        ;

        yield TextField::new('country', '国家')
            ->setHelp('用户所在国家')
            ->hideOnIndex()
        ;

        yield TextField::new('province', '省份')
            ->setHelp('用户所在省份')
        ;

        yield TextField::new('city', '城市')
            ->setHelp('用户所在城市')
        ;

        $sexField = EnumField::new('sex', '性别');
        $sexField->setEnumCases(GenderEnum::cases());
        $sexField->setHelp('用户性别');
        yield $sexField;

        yield BooleanField::new('isFollow', '是否关注')
            ->setHelp('用户是否关注了公众号')
        ;

        yield IntegerField::new('followTime', '关注时间')
            ->setHelp('用户关注的时间戳')
            ->hideOnIndex()
        ;

        yield IntegerField::new('tradedNum', '交易笔数')
            ->setHelp('用户累计交易笔数')
            ->hideOnIndex()
        ;

        yield MoneyField::new('tradeMoney', '交易金额')
            ->setCurrency('CNY')
            ->setHelp('用户累计交易金额')
            ->hideOnIndex()
        ;

        yield TextareaField::new('points', '积分信息')
            ->setHelp('用户积分详细信息')
            ->onlyOnDetail()
        ;

        yield AssociationField::new('account', '关联账号')
            ->setHelp('粉丝关联的系统账号')
        ;

        yield AssociationField::new('levelInfo', '等级信息')
            ->setHelp('用户的会员等级信息')
            ->hideOnIndex()
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
            ->setEntityLabelInSingular('粉丝')
            ->setEntityLabelInPlural('粉丝')
            ->setDefaultSort(['id' => 'DESC'])
            ->setPaginatorPageSize(20)
        ;
    }

    public function configureFilters(Filters $filters): Filters
    {
        return $filters
            ->add('userId')
            ->add('weixinOpenId')
            ->add('nick')
            ->add('country')
            ->add('province')
            ->add('city')
            ->add('sex')
            ->add('isFollow')
            ->add('account')
            ->add('levelInfo')
        ;
    }
}
