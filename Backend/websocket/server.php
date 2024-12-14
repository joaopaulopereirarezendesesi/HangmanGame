<?php

require 'vendor/autoload.php';

use Ratchet\Server\IoServer;
use Ratchet\Http\HttpServer;
use Ratchet\WebSocket\WsServer;
use api\Websocket\ChatHandler; 

$server = new IoServer(
    new HttpServer(
        new WsServer(
            new ChatHandler()
        )
    ),
    8080
);

$server->run();
