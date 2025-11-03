<?php

namespace YouzanApiUserBundle;

use Doctrine\Bundle\DoctrineBundle\DoctrineBundle;
use Symfony\Component\HttpKernel\Bundle\Bundle;
use Tourze\BundleDependency\BundleDependencyInterface;
use YouzanApiBundle\YouzanApiBundle;
use Tourze\EasyAdminMenuBundle\EasyAdminMenuBundle;

class YouzanApiUserBundle extends Bundle implements BundleDependencyInterface
{
    public static function getBundleDependencies(): array
    {
        return [
            YouzanApiBundle::class => ['all' => true],
            DoctrineBundle::class => ['all' => true],
            EasyAdminMenuBundle::class => ['all' => true],
        ];
    }
}
