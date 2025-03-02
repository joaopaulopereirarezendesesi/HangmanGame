<?php

namespace handler;

use Controller\WebSocketController;
use Ratchet\ConnectionInterface;
use models\WSModel;
use tools\Utils;

class RoomHandler
{
    private WebSocketController $wsController;
    private WSModel $WSModel;

    public function __construct(WebSocketController $wsController)
    {
        $this->WSModel = new WSModel();
        $this->wsController = $wsController;
    }

    public function handle(ConnectionInterface $conn, $id_bd): void
    {
        $this->wsController->users[$conn->resourceId] = [ 'id_bd' => $id_bd, 'connection' => $conn ];
        $this->WSModel->changeStatus($id_bd, true);
        Utils::displayMessage("Cliente {$conn->resourceId} fez login", 'player_join');
        $conn->send(json_encode(['success' => "Fez login com sucesso"]));
    }
}
