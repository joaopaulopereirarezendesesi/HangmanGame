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
        $toConnection = null;
        foreach ($this->wsController->users as $user) {
            if ($user['id_bd'] === $toUser) {
                $toConnection = $user['connection'];
                break;
            }
        }

        if ($toConnection) {
            $toConnection->send(json_encode([
                'type' => 'friendRequest',
                'fromUser' => $fromUser,
                'toUser' => $toUser,
                'status' => $status
            ]));

            Utils::displayMessage("Solicitação enviada para {$toUser}", 'success');
        } else {
            Utils::displayMessage("Usuário {$toUser} não encontrado online", 'error');
        }
    }
}
