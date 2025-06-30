<?php

namespace YouzanApiUserBundle\Tests\Unit;

use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpKernel\Bundle\Bundle;
use Tourze\BundleDependency\BundleDependencyInterface;
use YouzanApiUserBundle\YouzanApiUserBundle;

class YouzanApiUserBundleTest extends TestCase
{
    public function testBundleIsBundle(): void
    {
        $bundle = new YouzanApiUserBundle();
        
        $this->assertInstanceOf(Bundle::class, $bundle);
    }
    
    public function testBundleImplementsBundleDependencyInterface(): void
    {
        $bundle = new YouzanApiUserBundle();
        
        $this->assertInstanceOf(BundleDependencyInterface::class, $bundle);
    }
    
    public function testGetBundleDependencies(): void
    {
        $dependencies = YouzanApiUserBundle::getBundleDependencies();
        
        $this->assertArrayHasKey(\YouzanApiBundle\YouzanApiBundle::class, $dependencies);
        $this->assertSame(['all' => true], $dependencies[\YouzanApiBundle\YouzanApiBundle::class]);
    }
}