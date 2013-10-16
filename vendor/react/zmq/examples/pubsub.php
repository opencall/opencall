<?php

require __DIR__.'/../vendor/autoload.php';

$loop = React\EventLoop\Factory::create();

$context = new React\ZMQ\Context($loop);

$sub = $context->getSocket(ZMQ::SOCKET_SUB);
$sub->bind('tcp://127.0.0.1:5555');
$sub->subscribe('foo');

$sub->on('message', function ($msg) {
    echo "Received: $msg\n";
});

$pub = $context->getSocket(ZMQ::SOCKET_PUB);
$pub->connect('tcp://127.0.0.1:5555');

$i = 0;
$loop->addPeriodicTimer(1, function () use (&$i, $pub) {
    $i++;
    echo "publishing $i\n";
    $pub->send('foo '.$i);
});

$loop->run();
