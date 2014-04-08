<?php

require_once(__DIR__ . '/../../app/autoload.php');

use Plivo\Callback;
use Predis\Client as PredisClient;
use Symfony\Component\Yaml\Parser;

$yaml = new Parser();
$config = $yaml->parse(file_get_contents(__DIR__ . '/../../app/config/plivo.yml'));

// setup redis
$redis = new PredisClient($config['redis']['param']);

// setup mysql
$dsn = 'mysql:host=' . $config['database']['host'] . ';dbname=' . $config['database']['db_name'];
$user = $config['database']['user'];
$pass = $config['database']['pass'];
$pdo = new PDO($dsn, $user, $pass);

// zeromq
$zmq_server = $config['livelog']['zmq_server'];
$context = new ZMQContext();
$zmq_socket = $context->getSocket(ZMQ::SOCKET_PUSH, 'log_pusher');
$zmq_socket->connect($zmq_server);

$callback = new Callback($pdo, $redis, $zmq_socket);
$callback->run($_POST);
echo '';
