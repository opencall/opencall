<?php

require_once __DIR__ . '/../vendor/autoload.php';

use OnCall\Bundle\AdminBundle\Entity\CallLog;

$data = array(
    'topic' => 'client:20',
    'logentry' => $log;
);

$json_data = json_encode($data);

$context = new ZMQContext();
$socket = $context->getSocket(ZMQ::SOCKET_PUSH, 'pusher');
$socket->connect('tcp://localhost:5555');
$socket->send($json_data);

