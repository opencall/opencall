<?php

require __DIR__.'/../vendor/autoload.php';

$loop = React\EventLoop\Factory::create();

$context = new React\ZMQ\Context($loop);

$push = $context->getSocket(ZMQ::SOCKET_PUSH);
$push->connect('tcp://127.0.0.1:5555');

$push->on('error', function ($e) {
    var_dump($e->getMessage());
});

$i = 0;
$loop->addPeriodicTimer(1, function () use (&$i, $push) {
    $i++;
    echo "sending $i\n";
    $push->send($i);
});

$loop->run();
