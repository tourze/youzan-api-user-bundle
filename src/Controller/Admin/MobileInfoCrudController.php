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
use EasyCorp\Bundle\EasyAdminBundle\Field\TelephoneField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use YouzanApiUserBundle\Entity\MobileInfo;

#[AdminCrud(routePath: '/youzan-api-user/mobile-info', routeName: 'youzan_api_user_mobile_info')]
final class MobileInfoCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return MobileInfo::class;
    }

    public function configureFields(string $pageName): iterable
    {
        yield IdField::new('id', 'ID')->onlyOnIndex();

        yield TextField::new('countryCode', '国家代码')
            ->setHelp('手机号码的国家/地区代码（如：+86）')
        ;

        yield TelephoneField::new('mobileDecrypted', '手机号（明文）')
            ->setHelp('用户手机号码明文显示')
            ->hideOnIndex()
        ;

        yield TextField::new('mobileEncrypted', '手机号（加密）')
            ->setHelp('用户手机号码加密存储')
            ->onlyOnDetail()
        ;

        yield AssociationField::new('user', '关联用户')
            ->setHelp('手机信息关联的有赞用户')
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
            ->setEntityLabelInSingular('手机信息')
            ->setEntityLabelInPlural('手机信息')
            ->setDefaultSort(['id' => 'DESC'])
            ->setPaginatorPageSize(20)
        ;
    }

    public function configureFilters(Filters $filters): Filters
    {
        return $filters
            ->add('countryCode')
            ->add('mobileDecrypted')
            ->add('user')
        ;
    }
}
