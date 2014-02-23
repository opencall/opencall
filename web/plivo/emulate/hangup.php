<?php

require_once(__DIR__ . '/../../../app/autoload.php');
require_once(__DIR__ . '/../../../src/PHPMailer/PHPMailerAutoload.php');

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
$call_id = file_get_contents('/tmp/plivo.call_id');
$date_start = new DateTime();
$date_start->modify('-10 min');
$date_end = new DateTime();
$post = array(
    'CallUUID' => $call_id,
    'From' => '0000000000',
    'To' => '6531582180',
    'CallStatus' => 'cancel',
    'Direction' => 'inbound',
    'HangupCause' => 'ORIGINATOR_CANCEL',
    'Duration' => 42,
    'BillDuration' => 0,
    'BillRate' => '0.00400',
    'Event' => 'Hangup',
    'StartTime' => $date_start->format('Y-m-d H:i:s'),
    'EndTime' => $date_end->format('Y-m-d H:i:s')
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
