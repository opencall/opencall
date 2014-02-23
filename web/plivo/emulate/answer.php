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
$date = new DateTime();
$call_id = 'test-' . $date->format('YmdHis');
file_put_contents('/tmp/plivo.call_id', $call_id);
$post = array(
    'CallUUID' => $call_id,
    'From' => '0000000000',
    'To' => '6531582180',
    'CallStatus' => 'ringing',
    'Direction' => 'inbound',
    'BillRate' => '0.00400',
    'Event' => 'StartApp'
);

$answer = new Answer(
    $pdo,
    $zmq_socket,
    'http://dev.calltracking.hk/plivo/callback.php',
    'http://dev.calltracking.hk/plivo/record.php'
);
echo $answer->run($post);
