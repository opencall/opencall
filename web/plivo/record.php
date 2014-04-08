<?php

require_once(__DIR__ . '/../../app/autoload.php');

use Plivo\Record;
use Symfony\Component\Yaml\Parser;

$yaml = new Parser();
$config = $yaml->parse(file_get_contents(__DIR__ . '/../../app/config/plivo.yml'));

// setup mysql
$dsn = 'mysql:host=' . $config['database']['host'] . ';dbname=' . $config['database']['db_name'];
$user = $config['database']['user'];
$pass = $config['database']['pass'];
$pdo = new PDO($dsn, $user, $pass);

$rec = new Record($pdo);
$rec->run($_POST);

// error_log(print_r($_POST, true));
