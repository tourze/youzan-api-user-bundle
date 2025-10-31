<?php

declare(strict_types=1);

namespace YouzanApiUserBundle\Controller\Admin;

use EasyCorp\Bundle\EasyAdminBundle\Attribute\AdminCrud;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Config\Filters;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\EmailField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IntegerField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use YouzanApiUserBundle\Entity\Staff;

#[AdminCrud(routePath: '/youzan-api-user/staff', routeName: 'youzan_api_user_staff')]
final class StaffCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Staff::class;
    }

    public function configureFields(string $pageName): iterable
    {
        yield IdField::new('id', 'ID')->onlyOnIndex();

        yield TextField::new('corpName', '企业名称')
            ->setHelp('员工所属企业名称')
        ;

        yield IntegerField::new('kdtId', '微商城店铺ID')
            ->setHelp('有赞微商城店铺标识')
            ->hideOnIndex()
        ;

        yield TextField::new('corpId', '企业ID')
            ->setHelp('企业唯一标识符')
            ->hideOnIndex()
        ;

        yield EmailField::new('email', '员工邮箱')
            ->setHelp('员工联系邮箱地址')
        ;

        yield TextField::new('name', '员工名称')
            ->setHelp('员工姓名')
        ;

        yield AssociationField::new('user', '关联用户')
            ->setHelp('员工关联的有赞用户信息')
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
            ->setEntityLabelInSingular('员工')
            ->setEntityLabelInPlural('员工')
            ->setDefaultSort(['id' => 'DESC'])
            ->setPaginatorPageSize(20)
        ;
    }

    public function configureFilters(Filters $filters): Filters
    {
        return $filters
            ->add('corpName')
            ->add('kdtId')
            ->add('corpId')
            ->add('email')
            ->add('name')
            ->add('user')
        ;
    }
}
