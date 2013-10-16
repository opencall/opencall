<?php

namespace React\ZMQ;

class ContextTest extends \PHPUnit_Framework_TestCase
{
    /** @test */
    public function itShouldWrapARealZMQContext()
    {
        $loop = $this->getMock('React\EventLoop\LoopInterface');

        $innerContext = $this->getMockBuilder('ZMQContext')->disableOriginalConstructor()->getMock();
        $innerContext
            ->expects($this->once())
            ->method('getSocket')
            ->with(\ZMQ::SOCKET_PULL, null);

        $context = new Context($loop, $innerContext);
        $context->getSocket(\ZMQ::SOCKET_PULL, null);
    }

    /** @test */
    public function getSocketShouldWrapSockets()
    {
        $loop = $this->getMock('React\EventLoop\LoopInterface');

        $context = new Context($loop);
        $socket = $context->getSocket(\ZMQ::SOCKET_PULL);

        $this->assertInstanceOf('React\ZMQ\SocketWrapper', $socket);
    }

    /** @test */
    public function getSocketShouldAddReadListener()
    {
        $loop = $this->getMock('React\EventLoop\LoopInterface');
        $loop
            ->expects($this->once())
            ->method('addReadStream');

        $context = new Context($loop);
        $socket = $context->getSocket(\ZMQ::SOCKET_PULL);
    }

    /** @test */
    public function getSocketShouldNotAddReadListenerForNonReadableSocketType()
    {
        $loop = $this->getMock('React\EventLoop\LoopInterface');
        $loop
            ->expects($this->never())
            ->method('addReadStream');

        $context = new Context($loop);
        $socket = $context->getSocket(\ZMQ::SOCKET_PUSH);
    }
}
