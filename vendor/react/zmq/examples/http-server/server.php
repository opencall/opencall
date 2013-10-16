<?php

require 'vendor/autoload.php';

$loop = React\EventLoop\Factory::create();
$socket = new React\Socket\Server($loop);
$http = new React\Http\Server($socket);

$context = new React\ZMQ\Context($loop);
$dealer = $context->getSocket(ZMQ::SOCKET_DEALER);
$dealer->bind('tcp://127.0.0.1:4444');

$conns = new ArrayObject();

$dealer->on('messages', function ($msg) use ($conns) {
    list($hash, $blank, $data) = $msg;

    if (!isset($conns[$hash])) {
        return;
    }

    $response = $conns[$hash];
    $response->writeHead();
    $response->end($data);
});

$http->on('request', function ($request, $response) use ($dealer, $conns) {
    $hash = spl_object_hash($request);
    $conns[$hash] = $response;

    $request->on('end', function () use ($conns, $hash) {
        unset($conns[$hash]);
    });

    $dealer->send(array(
        $hash,
        '',
        $request->getPath()
    ));
});

$socket->listen(8080);
$loop->run();
