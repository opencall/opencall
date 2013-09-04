<?php
error_reporting(E_ALL);
ini_set('display_errors', '1');
$username = 'mongowebuser';
$password = '873qyh3qd';
$host = '54.254.140.2';



$db = new Mongo('mongodb://54.254.140.2', array(
    'username' => $username,
    'password' => $password,
    'db'       => 'calltracking-HK'
));


?>