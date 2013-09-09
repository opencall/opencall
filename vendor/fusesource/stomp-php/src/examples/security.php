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
/*
 To successfully run this example, you must first start the broker with security enabled.
 You can do that by executing:
 $ ${ACTIVEMQ_HOME}/bin/activemq xbean:activemq-security.xml
 Then you can execute this example with:
 $ php security.php
*/
// include a library
use FuseSource\Stomp\Stomp;
use FuseSource\Stomp\Exception\StompException;

// make a connection
$con = new Stomp("tcp://localhost:61613");
// use sync operations
$con->sync = true;
// connect
try {
    $con->connect("dejan", "test");
} catch (StompException $e) {
    echo "dejan cannot connect\n";
    echo $e->getMessage() . "\n";
    echo $e->getDetails() . "\n\n\n";
}

$con->connect("guest", "password");

// send a message to the queue
try {
    $con->send("/queue/test", "test");
    echo "Guest sent message with body 'test'\n";
} catch (StompException $e) {
    echo "guest cannot send\n";
    echo $e->getMessage() . "\n";
    echo $e->getDetails() . "\n\n\n";
}
// disconnect
$con->disconnect();


$con->connect("system", "manager");

// send a message to the queue
$con->send("/queue/test", "test");
echo "System manager sent message with body 'test'\n";

// disconnect
$con->disconnect();

?>