<?php

namespace handler;

use Controller\WebSocketController;
use Ratchet\ConnectionInterface;
use models\WSModel;
use tools\Utils;

class LoginHandler
{
    private WebSocketController $wsController;
    private WSModel $WSModel;

    public function __construct(WebSocketController $wsController)
    {
        $this->WSModel = new WSModel();
        $this->wsController = $wsController;
    }

    public function handle(ConnectionInterface $conn, $id_bd, $password): void
    {
        $passwordBD = $this->WSModel->verifiedIdPassword($id_bd);
        
        if (!$passwordBD) {
            $conn->send(json_encode(['error' => "User not found"]));
            return;
        }

        if (!password_verify($password, $passwordBD)) {
            $conn->send(json_encode(['error' => "Invalid password"]));
            return;
        }

        $this->wsController->clients[$conn->resourceId]['authenticated'] = true;
        $this->wsController->users[$conn->resourceId] = ['id_bd' => $id_bd, 'connection' => $conn];
        $this->WSModel->changeStatus($id_bd, true);

        Utils::displayMessage("Client {$conn->resourceId} logged in", 'player_join');
        $conn->send(json_encode(
            [
                'type' => 'login',
                'success' => true
            ]
        ));
    }
}
