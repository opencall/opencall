<?php

use Plivo\Callback;
use Predis\Client as PredisClient;

error_log('START callback dump');
error_log(print_r($_POST, true));
error_log('END callback dump');

require_once(__DIR__ . '/../../app/autoload.php');

// setup redis
$rconf = array(
    'scheme' => 'tcp',
    'host' => 'devredisnode.5zaozk.0001.apse1.cache.amazonaws.com',
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
