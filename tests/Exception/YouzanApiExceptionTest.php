<?php

namespace YouzanApiUserBundle\Tests\Exception;

use PHPUnit\Framework\Attributes\CoversClass;
use Tourze\PHPUnitBase\AbstractExceptionTestCase;
use YouzanApiUserBundle\Exception\YouzanApiException;

/**
 * @internal
 */
#[CoversClass(YouzanApiException::class)]
final class YouzanApiExceptionTest extends AbstractExceptionTestCase
{
    protected function onSetUp(): void
    {
        // 异常类测试不需要特殊设置
    }

    public function testExceptionIsRuntimeException(): void
    {
        $exception = new YouzanApiException();

        $this->assertNotNull($exception);
    }

    public function testExceptionWithMessage(): void
    {
        $message = 'Test exception message';
        $exception = new YouzanApiException($message);

        $this->assertSame($message, $exception->getMessage());
    }

    public function testExceptionWithMessageAndCode(): void
    {
        $message = 'Test exception message';
        $code = 123;
        $exception = new YouzanApiException($message, $code);

        $this->assertSame($message, $exception->getMessage());
        $this->assertSame($code, $exception->getCode());
    }
}
