<?php

namespace React\ZMQ;

use Evenement\EventEmitter;
use React\EventLoop\LoopInterface;

class SocketWrapper extends EventEmitter
{
    public $fd;
    public $closed = false;
    private $socket;
    private $loop;
    private $buffer;

    public function __construct(\ZMQSocket $socket, LoopInterface $loop)
    {
        $this->socket = $socket;
        $this->loop = $loop;

        $this->fd = $this->socket->getSockOpt(\ZMQ::SOCKOPT_FD);

        $writeListener = array($this, 'handleEvent');
        $this->buffer = new Buffer($socket, $this->fd, $this->loop, $writeListener);
    }

    public function attachReadListener()
    {
        $this->loop->addReadStream($this->fd, array($this, 'handleEvent'));
    }

    public function handleEvent()
    {
        while (true) {
            $events = $this->socket->getSockOpt(\ZMQ::SOCKOPT_EVENTS);

            $hasEvents = ($events & \ZMQ::POLL_IN) || ($events & \ZMQ::POLL_OUT && $this->buffer->listening);
            if (!$hasEvents) {
                break;
            }

            if ($events & \ZMQ::POLL_IN) {
                $this->handleReadEvent();
            }

            if ($events & \ZMQ::POLL_OUT && $this->buffer->listening) {
                $this->buffer->handleWriteEvent();
            }
        }
    }

    public function handleReadEvent()
    {
        $messages = $this->socket->recvmulti(\ZMQ::MODE_NOBLOCK);
        if (false !== $messages) {
            if (1 === count($messages)) {
                $this->emit('message', array($messages[0]));
            }
            $this->emit('messages', array($messages));
        }
    }

    public function getWrappedSocket()
    {
        return $this->socket;
    }

    public function subscribe($channel)
    {
        $this->socket->setSockOpt(\ZMQ::SOCKOPT_SUBSCRIBE, $channel);
    }

    public function unsubscribe($channel)
    {
        $this->socket->setSockOpt(\ZMQ::SOCKOPT_UNSUBSCRIBE, $channel);
    }

    public function send($message)
    {
        $this->buffer->send($message);
    }

    public function close()
    {
        if ($this->closed) {
            return;
        }

        $this->emit('end', array($this));
        $this->loop->removeStream($this->fd);
        $this->buffer->removeAllListeners();
        $this->removeAllListeners();
        unset($this->socket);
        $this->closed = true;
    }

    public function end()
    {
        if ($this->closed) {
            return;
        }

        $that = $this;

        $this->buffer->on('end', function () use ($that) {
            $that->close();
        });

        $this->buffer->end();
    }

    public function __call($method, $args)
    {
        return call_user_func_array(array($this->socket, $method), $args);
    }
}
