<?php

namespace YouzanApiUserBundle\Tests\Unit\Exception;

use PHPUnit\Framework\TestCase;
use YouzanApiUserBundle\Exception\YouzanApiException;

class YouzanApiExceptionTest extends TestCase
{
    public function testExceptionIsRuntimeException(): void
    {
        $exception = new YouzanApiException();
        
        $this->assertInstanceOf(\RuntimeException::class, $exception);
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