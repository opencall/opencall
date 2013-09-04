<?php

// Include the Stomp-PHP classes to use the 
// Stomp protocol
require_once 'Stomp.php';

define ('ACTIVEMQ_SERVER','54.254.140.2');

// Create a new Instance
$con = new Stomp('tcp://'.ACTIVEMQ_SERVER.':61613');

/**
* For sending a message to the que
*/
$que_name = '<name of the queue>';
$message = 'message';

$con->connect();
$con->send("/queue/".$que_name, $message);
	
	
/**
* For recieving a message from the que
*/
$con->connect();
$con->subscribe("/queue/$que_name");
$message = $con->readFrame();

// Acknowledge that you read the message
$con->ack($msg);
// Disconnect
$con->disconnect();


?>