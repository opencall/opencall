<?php

namespace React\ZMQ;

class SocketWrapperTest extends \PHPUnit_Framework_TestCase
{
    /** @test */
    public function itShouldWrapARealZMQSocket()
    {
        $loop = $this->getMock('React\EventLoop\LoopInterface');

        $socket = $this->getMockBuilder('ZMQSocket')->disableOriginalConstructor()->getMock();
        $socket
            ->expects($this->once())
            ->method('connect')
            ->with('tcp://127.0.0.1:5555');

        $wrapped = new SocketWrapper($socket, $loop);
        $wrapped->connect('tcp://127.0.0.1:5555');
    }

    /** @test */
    public function subscribeShouldSetSocketOption()
    {
        $loop = $this->getMock('React\EventLoop\LoopInterface');

        $socket = $this->getMockBuilder('ZMQSocket')->disableOriginalConstructor()->getMock();
        $socket
            ->expects($this->once())
            ->method('setSockOpt')
            ->with(\ZMQ::SOCKOPT_SUBSCRIBE, 'foo');

        $wrapped = new SocketWrapper($socket, $loop);
        $wrapped->subscribe('foo');
    }

    /** @test */
    public function unsubscribeShouldSetSocketOption()
    {
        $loop = $this->getMock('React\EventLoop\LoopInterface');

        $socket = $this->getMockBuilder('ZMQSocket')->disableOriginalConstructor()->getMock();
        $socket
            ->expects($this->once())
            ->method('setSockOpt')
            ->with(\ZMQ::SOCKOPT_UNSUBSCRIBE, 'foo');

        $wrapped = new SocketWrapper($socket, $loop);
        $wrapped->unsubscribe('foo');
    }

    /** @test */
    public function sendShouldBufferMessages()
    {
        $loop = $this->getMock('React\EventLoop\LoopInterface');
        $loop
            ->expects($this->once())
            ->method('addWriteStream')
            ->with(14);

        $socket = $this->getMockBuilder('ZMQSocket')->disableOriginalConstructor()->getMock();
        $socket
            ->expects($this->any())
            ->method('getSockOpt')
            ->with(\ZMQ::SOCKOPT_FD)
            ->will($this->returnValue(14));

        $wrapped = new SocketWrapper($socket, $loop);
        $wrapped->send('foobar');
    }

    /** @test */
    public function closeShouldStopListening()
    {
        $loop = $this->getMock('React\EventLoop\LoopInterface');
        $loop
            ->expects($this->once())
            ->method('removeStream')
            ->with(14);

        $socket = $this->getMockBuilder('ZMQSocket')->disableOriginalConstructor()->getMock();
        $socket
            ->expects($this->any())
            ->method('getSockOpt')
            ->with(\ZMQ::SOCKOPT_FD)
            ->will($this->returnValue(14));

        $wrapped = new SocketWrapper($socket, $loop);
        $wrapped->close();
    }
}
