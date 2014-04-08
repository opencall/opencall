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
use Symfony\Component\Yaml\Parser;

$pid = pcntl_fork();

if ($pid == -11)
{
    return 1;
}
else if ($pid)
{
}
else
{
    echo "websocket server up and running.\n";

    $yaml = new Parser();
    $config = $yaml->parse(file_get_contents(__DIR__ . '/../app/config/plivo.yml'));

    // setup mysql
    $dsn = 'mysql:host=' . $config['database']['host'] . ';dbname=' . $config['database']['db_name'];
    $user = $config['database']['user'];
    $pass = $config['database']['pass'];
    $pdo = new PDO($dsn, $user, $pass);

    $loop = EventLoopFactory::create();
    $pusher = new Pusher(new LogRepo($pdo));

    $context = new Context($loop);
    $pull = $context->getSocket(ZMQ::SOCKET_PULL);
    $pull->bind($config['livelog']['zmq_server']);
    $pull->on('message', array($pusher, 'onLogEntry'));

    $websock = new Server($loop);
    $websock->listen($config['livelog']['websocket_port'], $config['livelog']['websocket_ip']);
    $server = new IoServer(
        new HttpServer(
            new WsServer(
                new WampServer($pusher)
            )
        ),
        $websock
    );

    $loop->run();
}
