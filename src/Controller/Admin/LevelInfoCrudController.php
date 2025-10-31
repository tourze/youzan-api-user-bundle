<?php

declare(strict_types=1);

namespace YouzanApiUserBundle\Controller\Admin;

use EasyCorp\Bundle\EasyAdminBundle\Attribute\AdminCrud;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Config\Filters;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IntegerField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use YouzanApiUserBundle\Entity\LevelInfo;

#[AdminCrud(routePath: '/youzan-api-user/level-info', routeName: 'youzan_api_user_level_info')]
final class LevelInfoCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return LevelInfo::class;
    }

    public function configureFields(string $pageName): iterable
    {
        yield IdField::new('id', 'ID')->onlyOnIndex();

        yield IntegerField::new('levelId', '会员等级ID')
            ->setHelp('有赞平台的会员等级标识符')
        ;

        yield TextField::new('levelName', '会员等级名称')
            ->setHelp('会员等级的显示名称')
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
            ->setEntityLabelInSingular('会员等级')
            ->setEntityLabelInPlural('会员等级')
            ->setDefaultSort(['id' => 'DESC'])
            ->setPaginatorPageSize(20)
        ;
    }

    public function configureFilters(Filters $filters): Filters
    {
        return $filters
            ->add('levelId')
            ->add('levelName')
        ;
    }
}
