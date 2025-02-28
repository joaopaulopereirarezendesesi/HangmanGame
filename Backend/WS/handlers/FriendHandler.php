<?php

namespace handler;

use Controller\WebSocketController;
use models\WSModel;
use tools\Utils;

class FriendHandler
{
    private WebSocketController $wsController;
    private WSModel $WSModel;

    public function __construct(WebSocketController $wsController)
    {
        $this->WSModel = new WSModel();
        $this->wsController = $wsController;
    }
    public function handle(string $fromUser, string $toUser)
    {
        Utils::displayMessage("Processando solicitação de amizade de {$fromUser} para {$toUser}", 'info');

        $result = $this->WSModel->sendFriendRequest($fromUser, $toUser);
        $status = $result ? 'enviada' : 'já existe';

        foreach ($this->wsController->clients as $client) {
            $client->send(json_encode([
                'type' => 'friendRequest',
                'fromUser' => $fromUser,
                'toUser' => $toUser,
                'status' => $status
            ]));
        }

        $message = $result ? "Solicitação enviada com sucesso" : "Solicitação já existe";
        Utils::displayMessage($message, $result ? 'success' : 'error');
    }
}
