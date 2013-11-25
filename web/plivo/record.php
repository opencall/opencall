<?php

require_once(__DIR__ . '/../../app/autoload.php');

use Plivo\Record;

// setup mysql
$dsn = 'mysql:host=db.oncall;dbname=oncall';
$user = 'webuser';
$pass = 'lks8jw23';
$pdo = new PDO($dsn, $user, $pass);

$rec = new Record($pdo);
$rec->run($_POST);

// error_log(print_r($_POST, true));
