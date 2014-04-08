<?php

require_once(__DIR__ . '/../../app/autoload.php');

use Plivo\Answer;
use Symfony\Component\Yaml\Parser;

$yaml = new Parser();
$config = $yaml->parse(file_get_contents(__DIR__ . '/../../app/config/plivo.yml'));

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

$answer = new Answer(
    $pdo,
    $zmq_socket,
    $config['url']['callback'],
    $config['url']['record']
);
echo $answer->run($_POST);
