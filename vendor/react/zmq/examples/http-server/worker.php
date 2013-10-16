<?php

$context = new ZMQContext();
$rep = $context->getSocket(ZMQ::SOCKET_REP);
$rep->connect('tcp://127.0.0.1:4444');

while (true) {
    $msg = $rep->recv();
    var_dump($msg);
    $rep->send("LULZ\n");
}
