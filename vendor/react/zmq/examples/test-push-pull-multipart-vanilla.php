<?php
// Test script doing a vanilla-ZMQ push of both single and
// multipart messages while pulling in React.
//
// For each push a lowercase s (single) or m (multipart)
// is printed. For each pull a uppercase S (single)
// or M (multipart) is printed.
//
// The expected output is a chaos of 's' and 'm' and equal
// amounts of 'S' and 'M' as the messages are pulled.
//
// What might happen: A multipart message is not read as 
// multipart, so you'll never see the 'M'. If the ZMQ communication
// breaks down you'll only see lowercase letters.

require __DIR__.'/../vendor/autoload.php';

function pull_routine()
{
    $loop = React\EventLoop\Factory::create();

    $context = new React\ZMQ\Context($loop);
    $socket = $context->getSocket(ZMQ::SOCKET_PULL);
    $socket->bind('ipc://test.ipc');
    $socket->on('message', function($msg) {
        if (is_array($msg))
            echo "M";
        else
            echo "S";
    });

    $loop->run();
}

function push_routine()
{
    $zmq = new ZMQContext(1);
    $socket = $zmq->getSocket(ZMQ::SOCKET_PUSH, 'xyz');
    $socket->connect('ipc://test.ipc');

    while (true) {
        $msgs = rand(1, 300);
        for ($n = 0; $n < $msgs; $n++) {
            if (rand(0,100) >= 50) {
                echo "s";
                $socket->send('bogus-'.$n);
            }
            else {
                echo "m";
                $socket->sendmulti(array("bogus$n-1", "bogus$n-2", "bogus$n-3"));
            }
        }

        usleep(rand(0, 1000000));
    }
}

$pid = pcntl_fork();
if ($pid == 0) {
    pull_routine();
    exit;
}

push_routine();
