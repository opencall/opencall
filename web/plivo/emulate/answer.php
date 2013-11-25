<?php

require_once(__DIR__ . '/../../../app/autoload.php');

use Plivo\Answer;

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


// emulated post
$post = array(
    'CallUUID' => 'test-230948029348902',
    'From' => '0000000000',
    'To' => '85235009085',
    'CallStatus' => 'ringing',
    'Direction' => 'inbound',
    'BillRate' => '0.00400',
    'Event' => 'StartApp'
);

$answer = new Answer($pdo, $zmq_socket, 'http://beta.calltracking.hk/plivo/callback.php');
echo $answer->run($post);
