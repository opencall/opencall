<?php

namespace React\ZMQ;

class BufferTest extends \PHPUnit_Framework_TestCase
{
    /** @test */
    public function sendShouldQueueMessages()
    {
        $loop = $this->getMock('React\EventLoop\LoopInterface');
        $loop
            ->expects($this->once())
            ->method('addWriteStream');

        $socket = $this->getMockBuilder('ZMQSocket')->disableOriginalConstructor()->getMock();
        $socket
            ->expects($this->never())
            ->method('send');

        $writeListener = function () {};

        $buffer = new Buffer($socket, 42, $loop, $writeListener);
        $buffer->send('foo');
    }

    /** @test */
    public function loopShouldSendQueuedMessages()
    {
        $writeListener = function () {};

        $loop = $this->getMock('React\EventLoop\LoopInterface');
        $loop
            ->expects($this->once())
            ->method('addWriteStream')
            ->with($this->isType('integer'), $writeListener);

        $socket = $this->getMockBuilder('ZMQSocket')->disableOriginalConstructor()->getMock();
        $socket
            ->expects($this->at(0))
            ->method('sendmulti')
            ->with(array('foo'), \ZMQ::MODE_DONTWAIT)
            ->will($this->returnSelf());
        $socket
            ->expects($this->at(1))
            ->method('sendmulti')
            ->with(array('bar'), \ZMQ::MODE_DONTWAIT)
            ->will($this->returnSelf());

        $buffer = new Buffer($socket, 42, $loop, $writeListener);
        $buffer->send('foo');
        $buffer->send('bar');

        $buffer->handleWriteEvent();
    }
}
