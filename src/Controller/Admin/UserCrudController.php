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
use EasyCorp\Bundle\EasyAdminBundle\Field\IntegerField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Field\UrlField;
use Tourze\EasyAdminEnumFieldBundle\Field\EnumField;
use YouzanApiUserBundle\Entity\User;
use YouzanApiUserBundle\Enum\GenderEnum;

#[AdminCrud(routePath: '/youzan-api-user/user', routeName: 'youzan_api_user_user')]
final class UserCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return User::class;
    }

    public function configureFields(string $pageName): iterable
    {
        yield IdField::new('id', 'ID')->onlyOnIndex();

        yield TextField::new('yzOpenId', '有赞用户ID')
            ->setHelp('用户在有赞平台的唯一标识符')
        ;

        yield TextField::new('nickNameDecrypted', '昵称（明文）')
            ->setHelp('用户昵称的明文显示')
            ->hideOnIndex()
        ;

        yield TextField::new('nickNameEncrypted', '昵称（加密）')
            ->setHelp('用户昵称的加密存储')
            ->onlyOnDetail()
        ;

        yield UrlField::new('avatar', '头像')
            ->setHelp('用户头像链接地址')
        ;

        yield TextField::new('country', '国家')
            ->setHelp('用户所在国家')
        ;

        yield TextField::new('province', '省份')
            ->setHelp('用户所在省份')
        ;

        yield TextField::new('city', '城市')
            ->setHelp('用户所在城市')
        ;

        $genderField = EnumField::new('gender', '性别');
        $genderField->setEnumCases(GenderEnum::cases());
        $genderField->setHelp('用户性别');
        yield $genderField;

        yield IntegerField::new('platformType', '平台类型')
            ->setHelp('用户来源平台类型标识')
        ;

        yield AssociationField::new('account', '关联账号')
            ->setHelp('用户关联的系统账号')
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
            ->setEntityLabelInSingular('有赞用户')
            ->setEntityLabelInPlural('有赞用户')
            ->setDefaultSort(['id' => 'DESC'])
            ->setPaginatorPageSize(20)
        ;
    }

    public function configureFilters(Filters $filters): Filters
    {
        return $filters
            ->add('yzOpenId')
            ->add('nickNameDecrypted')
            ->add('country')
            ->add('province')
            ->add('city')
            ->add('gender')
            ->add('platformType')
            ->add('account')
        ;
    }
}
