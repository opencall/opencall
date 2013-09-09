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
// make a connection
$con = new Stomp("tcp://localhost:61613");
// connect
$con->connect();
$con->setReadTimeout(1);

// subscribe to the queue
$con->subscribe("/queue/transactions", array('ack' => 'client','activemq.prefetchSize' => 1 ));

// try to send some messages
$con->begin("tx1");
for ($i = 1; $i < 3; $i++) {
    $con->send("/queue/transactions", $i, array("transaction" => "tx1"));
}
// if we abort transaction, messages will not be sent
$con->abort("tx1");

// now send some messages for real
$con->begin("tx2");
echo "Sent messages {\n";
for ($i = 1; $i < 5; $i++) {
    echo "\t$i\n";
    $con->send("/queue/transactions", $i, array("transaction" => "tx2"));
}
echo "}\n";
// they will be available for consumers after commit
$con->commit("tx2"); 

// try to receive some messages
$con->begin("tx3");
$messages = array();
for ($i = 1; $i < 3; $i++) {
    $msg = $con->readFrame();
    array_push($messages, $msg);
    $con->ack($msg, "tx3");
}
// of we abort transaction, we will "rollback" out acks
$con->abort("tx3");

$con->begin("tx4");
// so we need to ack received messages again
// before we can receive more (prefetch = 1)
if (count($messages) != 0) {
    foreach($messages as $msg) {
        $con->ack($msg, "tx4");
    }
}
// now receive more messages
for ($i = 1; $i < 3; $i++) {
    $msg = $con->readFrame();
    $con->ack($msg, "tx4");
    array_push($messages, $msg);
}
// commit all acks
$con->commit("tx4");


echo "Processed messages {\n";
foreach($messages as $msg) {
    echo "\t$msg->body\n";
}
echo "}\n";

//ensure there are no more messages in the queue
$frame = $con->readFrame();

if ($frame === false) {
    echo "No more messages in the queue\n";
} else {
    echo "Warning: some messages still in the queue: $frame\n";
}

// disconnect
$con->disconnect();
?>