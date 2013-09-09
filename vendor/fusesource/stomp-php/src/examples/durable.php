<?php
require_once __DIR__.'/loader.php';
/**
 *
 * Copyright (C) 2009 Progress Software, Inc. All rights reserved.
 * http://fusesource.com
 *
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 *
 * http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 */

// include a library

use FuseSource\Stomp\Stomp;

// create a producer
$producer = new Stomp("tcp://localhost:61613");
// create a consumer
$consumer = new Stomp("tcp://localhost:61613");
$consumer->setReadTimeout(1);
// set clientId on a consumer to make it durable
$consumer->clientId = "test";
// connect
$producer->connect();
$consumer->connect();
// subscribe to the topic
$consumer->subscribe("/topic/test");

sleep(1);

// send a message to the topic
$producer->send("/topic/test", "test", array('persistent'=>'true'));
echo "Message 'test' sent to topic\n";

// receive a message from the topic
$msg = $consumer->readFrame();

// do what you want with the message
if ( $msg != null) {
    echo "Message '$msg->body' received from topic\n";
	$consumer->ack($msg);
} else {
    echo "Failed to receive a message\n";
}

sleep(1);

// disconnect durable consumer
$consumer->unsubscribe("/topic/test");
$consumer->disconnect();
echo "Disconnecting consumer\n";

// send a message while consumer is disconnected
// note: only persistent messages will be redelivered to the durable consumer
$producer->send("/topic/test", "test1", array('persistent'=>'true'));
echo "Message 'test1' sent to topic\n";


// reconnect the durable consumer
$consumer = new Stomp("tcp://localhost:61613");
$consumer->clientId = "test";
$consumer->connect();
$consumer->subscribe("/topic/test");
echo "Reconnecting consumer\n";

// receive a message from the topic
$msg = $consumer->readFrame();

// do what you want with the message
if ( $msg != null) {
    echo "Message '$msg->body' received from topic\n";
	$consumer->ack($msg);
} else {
    echo "Failed to receive a message\n";
}

// disconnect
$consumer->unsubscribe("/topic/test");
$consumer->disconnect();
$producer->disconnect();

?>