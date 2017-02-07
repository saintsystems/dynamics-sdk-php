<?php
use PHPUnit\Framework\TestCase;
use Microsoft\Dynamics\Exception\DynamicsException;

class DynamicsExceptionTest extends TestCase
{
    public function testToString()
    {
        $exception = new DynamicsException('bad stuff', '404');
        $this->assertEquals("Microsoft\Dynamics\Exception\DynamicsException: [404]: bad stuff\n", $exception->__toString());
    }
}
