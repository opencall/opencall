<?php

require_once __DIR__ . '/../vendor/autoload.php';

use Ratchet\Server\IoServer;
use Ratchet\Wamp\WampServer;
use Ratchet\WebSocket\WsServer;
use Ratchet\Http\HttpServer;
use React\Socket\Server;
use React\EventLoop\Factory as EventLoopFactory;
use React\ZMQ\Context;
use LiveLog\Pusher;
use Plivo\Log\Repository as LogRepo;

// pdo setup
$dsn = 'mysql:host=db.oncall;dbname=oncall';
$user = 'webuser';
$pass = 'lks8jw23';
$pdo = new PDO($dsn, $user, $pass);

$loop = EventLoopFactory::create();
$pusher = new Pusher(new LogRepo($pdo));

$context = new Context($loop);
$pull = $context->getSocket(ZMQ::SOCKET_PULL);
$pull->bind('tcp://127.0.0.1:5555');
$pull->on('message', array($pusher, 'onLogEntry'));

$websock = new Server($loop);
$websock->listen(8080, '0.0.0.0');
$server = new IoServer(
    new HttpServer(
        new WsServer(
            new WampServer($pusher)
        )
    ),
    $websock
);

$loop->run();
