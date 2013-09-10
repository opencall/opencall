<?php

require_once(__DIR__ . '/../app/autoload.php');

use OnCall\QueueHandler;

$data = array(
    'sample' => 'test',
    '1' => '1233409',
    'number' => 123,
    'float' => 213.00
);

$sender = new QueueHandler('tcp://localhost:61613');
$sender->setUser('guest')
    ->setPass('guest')
    ->connect()
    ->send($data);
