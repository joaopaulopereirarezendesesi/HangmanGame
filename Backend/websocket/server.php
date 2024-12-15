<?php

require 'vendor/autoload.php';

use Ratchet\Server\IoServer;
use Ratchet\Http\HttpServer;
use Ratchet\WebSocket\WsServer;
use api\Websocket\WShandler;  

$server = new IoServer(
    new HttpServer(
        new WsServer(
            new WShandler()  
        )
    ),
    8080
);

$server->run();
