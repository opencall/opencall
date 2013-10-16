<?php

namespace React\ZMQ;

use React\EventLoop\LoopInterface;

class Context
{
    private $loop;
    private $context;

    public function __construct(LoopInterface $loop, \ZMQContext $context = null)
    {
        $this->loop = $loop;
        $this->context = $context ?: new \ZMQContext();
    }

    public function __call($method, $args)
    {
        $res = call_user_func_array(array($this->context, $method), $args);
        if ($res instanceof \ZMQSocket) {
            $res = $this->wrapSocket($res);
        }
        return $res;
    }

    private function wrapSocket(\ZMQSocket $socket)
    {
        $wrapped = new SocketWrapper($socket, $this->loop);

        if ($this->isReadableSocketType($socket->getSocketType())) {
            $wrapped->attachReadListener();
        }

        return $wrapped;
    }

    private function isReadableSocketType($type)
    {
        $readableTypes = array(
            \ZMQ::SOCKET_PULL,
            \ZMQ::SOCKET_SUB,
            \ZMQ::SOCKET_REQ,
            \ZMQ::SOCKET_REP,
            \ZMQ::SOCKET_ROUTER,
            \ZMQ::SOCKET_DEALER,
        );

        return in_array($type, $readableTypes);
    }
}
