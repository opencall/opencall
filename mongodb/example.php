<?php
$username = $container->getParameter('mongo.username');
$password = $container->getParameter('mongo.password');
$host = $container->getParameter('mongo.host');
$mongoUrl = $container->getParameter('mongo.url');
$db = $container->getParameter('mongo.db');

$db = new Mongo($mongoUrl, array(
    'username' => $username,
    'password' => $password,
    'db'       => $db
));
