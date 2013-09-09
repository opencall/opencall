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
use FuseSource\Stomp\Message\Map;
// make a connection
$con = new Stomp("tcp://localhost:61613");
// connect
$con->connect();
// send a message to the queue
$body = array("city"=>"Belgrade", "name"=>"Dejan");
$header = array();
$header['transformation'] = 'jms-map-json';
$mapMessage = new Map($body, $header);
$con->send("/queue/test", $mapMessage);
echo "Sending array: ";
print_r($body);

$con->subscribe("/queue/test", array('transformation' => 'jms-map-json'));
$msg = $con->readFrame();

// extract 
if ( $msg != null) {
    echo "Received array: "; 
    print_r($msg->map);
    // mark the message as received in the queue
    $con->ack($msg);
} else {
    echo "Failed to receive a message\n";
}

// disconnect
$con->disconnect();
?>