<?php

use Plivo\Callback;
use Predis\Client as PredisClient;

require_once(__DIR__ . '/../../app/autoload.php');

// setup redis
$rconf = array(
    'scheme' => 'tcp',
    'host' => 'localhost',
    'port' => 6379
);
$redis = new PredisClient($rconf);

// setup mysql
$dsn = 'mysql:host=db.oncall;dbname=oncall';
$user = 'webuser';
$pass = 'lks8jw23';
$pdo = new PDO($dsn, $user, $pass);

// zeromq
$zmq_server = 'tcp://localhost:5555';
$context = new ZMQContext();
$zmq_socket = $context->getSocket(ZMQ::SOCKET_PUSH, 'log_pusher');
$zmq_socket->connect($zmq_server);

$callback = new Callback($pdo, $redis, $zmq_socket);
$callback->run($_POST);
echo '';
