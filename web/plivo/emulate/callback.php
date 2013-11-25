<?php

use Plivo\Callback;
use Predis\Client as PredisClient;

error_log('START callback dump');
error_log(print_r($_POST, true));
error_log('END callback dump');

require_once(__DIR__ . '/../../../app/autoload.php');

// setup mysql
$dsn = 'mysql:host=db.oncall;dbname=oncall';
$user = 'webuser';
$pass = 'lks8jw23';
$pdo = new PDO($dsn, $user, $pass);

// redis
$redis = new PredisClient();

// zeromq
$zmq_server = 'tcp://localhost:5555';
$context = new ZMQContext();
$zmq_socket = $context->getSocket(ZMQ::SOCKET_PUSH, 'log_pusher');
$zmq_socket->connect($zmq_server);

// emulated post
$call_id = file_get_contents('/tmp/plivo.call_id');
$post = array(
    'CallUUID' => $call_id,
    'DialBLegStatus' => 'hangup',
    'DialAction' => 'hangup',
    'DialBLegHangupCause' => 'NORMAL_CLEARING'
);

$callback = new Callback($pdo, $redis, $zmq_socket);
$callback->run($post);
echo '';
