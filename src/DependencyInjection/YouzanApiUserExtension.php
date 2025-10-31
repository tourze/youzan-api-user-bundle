<?php

namespace YouzanApiUserBundle\DependencyInjection;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Tourze\SymfonyDependencyServiceLoader\AutoExtension;

class YouzanApiUserExtension extends AutoExtension
{
    protected function getConfigDir(): string
    {
        return __DIR__ . '/../Resources/config';
    }
}
