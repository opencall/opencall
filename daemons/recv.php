<?php

require_once(__DIR__ . '/../app/autoload.php');

use OnCall\QueueHandler;

$qh = new QueueHandler('tcp://localhost:61613');
$qh->setUser('guest')
    ->setPass('guest')
    ->connect();

$data = $qh->recv();
print_r($data);
