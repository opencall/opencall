<?php
// Test script doing a periodic React-ZMQ push of both single and
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
    $socket->bind('ipc://test2.ipc');
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
    $loop = React\EventLoop\Factory::create();

    $context = new React\ZMQ\Context($loop);
    $socket = $context->getSocket(ZMQ::SOCKET_PUSH);
    $socket->connect('ipc://test2.ipc');

    $loop->addPeriodicTimer(1, function () use ($socket) {
        for ($n = 0; $n < rand(1, 30000); $n++) {
            if (rand(0,100) >= 50) {
                echo "s";
                $socket->send('bogus-'.$n);
            }
            else {
                echo "m";
                $socket->send(array("bogus$n-1", "bogus$n-2", "bogus$n-3"));
            }
        }
    });

    $loop->run();

}

$pid = pcntl_fork();
if ($pid == 0) {
    pull_routine();
    exit;
}

push_routine();
