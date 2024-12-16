<?php

require __DIR__ . '/vendor/autoload.php';  

use Ratchet\Server\IoServer;
use Ratchet\Http\HttpServer;
use Ratchet\WebSocket\WsServer;
use api\Websocket\WShandler;

use React\Socket\Server as ReactServer;
use React\EventLoop\Factory as EventLoopFactory;

print_r("Tudo funcionando!");

$loop = EventLoopFactory::create();

$socket = new ReactServer('0.0.0.0:8000', $loop);

$server = new IoServer(
    new HttpServer(
        new WsServer(
            new WShandler()  
        )
    ),
    $socket,  
    $loop    
);

$server->run();