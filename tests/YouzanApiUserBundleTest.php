<?php

declare(strict_types=1);

namespace YouzanApiUserBundle\Tests;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\RunTestsInSeparateProcesses;
use Tourze\PHPUnitSymfonyKernelTest\AbstractBundleTestCase;
use YouzanApiUserBundle\YouzanApiUserBundle;

/**
 * @internal
 */
#[CoversClass(YouzanApiUserBundle::class)]
#[RunTestsInSeparateProcesses]
final class YouzanApiUserBundleTest extends AbstractBundleTestCase
{
}
