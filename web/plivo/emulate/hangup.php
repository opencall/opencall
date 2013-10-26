<?php

require_once(__DIR__ . '/../../../app/autoload.php');

use Predis\Client as PredisClient;
use Plivo\Hangup;

// config stuff

// zeromq
$zmq_server = 'tcp://localhost:5555';
$context = new ZMQContext();
$zmq_socket = $context->getSocket(ZMQ::SOCKET_PUSH, 'log_pusher');
$zmq_socket->connect($zmq_server);

// redis
$redis = new PredisClient();

// emulated post
$post = array(
    'CallUUID' => 'test-230948029348902',
    'From' => '0000000000',
    'To' => '85235009087',
    'CallStatus' => 'cancel',
    'Direction' => 'inbound',
    'HangupCause' => 'ORIGINATOR_CANCEL',
    'Duration' => 0,
    'BillDuration' => 0,
    'BillRate' => '0.00400',
    'Event' => 'Hangup',
    'StartTime' => '2013-10-26 06:50:00',
    'EndTime' => '2013-10-26 09:28:45',
);

// setup mysql
$dsn = 'mysql:host=db.oncall;dbname=oncall';
$user = 'webuser';
$pass = 'lks8jw23';
$pdo = new PDO($dsn, $user, $pass);

// hangup
$hangup = new Hangup($pdo, $redis, $zmq_socket);
$hangup->run($post);

echo '';
