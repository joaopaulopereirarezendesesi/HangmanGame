<?php

require_once __DIR__ . '/vendor/autoload.php';
require_once __DIR__ . '/tools/Utils.php';

use Ratchet\Server\IoServer;
use Ratchet\Http\HttpServer;
use Ratchet\WebSocket\WsServer;
use controller\WebSocketController;

use React\Socket\Server as ReactServer;
use React\EventLoop\Factory as EventLoopFactory;

$loop = EventLoopFactory::create();

$socket = new ReactServer('0.0.0.0:8000', $loop);

$server = new IoServer(
    new HttpServer(
        new WsServer(
            new WebSocketController()
        )
    ),
    $socket,
    $loop
);

tools\Utils::displayMessage("WebSocket server started!\n", 'success');

$server->run();
