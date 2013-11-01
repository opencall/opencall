<?php

require_once __DIR__ . '/../vendor/autoload.php';

use Plivo\AccountCounter\Repository as CounterRepo;

// pdo setup
$dsn = 'mysql:host=db.oncall;dbname=oncall';
$user = 'webuser';
$pass = 'lks8jw23';
$pdo = new PDO($dsn, $user, $pass);

// initialize all counters based on today's date
$repo = new CounterRepo($pdo);
$repo->initializeAll(new DateTime());
