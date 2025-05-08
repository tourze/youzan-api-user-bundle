<?php

namespace YouzanApiUserBundle;

use Symfony\Component\HttpKernel\Bundle\Bundle;
use Tourze\BundleDependency\BundleDependencyInterface;

class YouzanApiUserBundle extends Bundle implements BundleDependencyInterface
{
    public static function getBundleDependencies(): array
    {
        return [
            \YouzanApiBundle\YouzanApiBundle::class => ['all' => true],
        ];
    }
}
