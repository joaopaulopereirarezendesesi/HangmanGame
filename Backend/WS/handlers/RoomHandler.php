<?php

namespace handler;

use Controller\WebSocketController;
use Ratchet\ConnectionInterface;
use tools\Utils;

class RoomHandler
{
    private WebSocketController $wsController;

    public function __construct(WebSocketController $wsController)
    {
        $this->wsController = $wsController;
    }

    public function handle(ConnectionInterface $conn, string $room): void
    {
        $this->wsController->rooms[$conn->resourceId] = $room;
        Utils::displayMessage("Client {$conn->resourceId} joined room {$room}", 'player_join');
        $conn->send(json_encode(['success' => "Joined room {$room}"]));
    }
}
