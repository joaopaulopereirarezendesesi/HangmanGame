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

    public function handle($fromUser, $toUser, $actionRequest, $response = null)
    {
        switch ($actionRequest) {
            case 'friendrequest':
                Utils::displayMessage("Processando solicitação de amizade de {$fromUser} para {$toUser}", 'info');

                $result = $this->WSModel->sendFriendRequest($fromUser, $toUser);
                $status = $result ? 'enviada' : 'já existe';

                $toConnection = $this->getUserConnection($toUser);

                if ($toConnection) {
                    $this->sendFriendRequestResponse($toConnection, $fromUser, $toUser);
                    Utils::displayMessage("Solicitação enviada para {$toUser}", 'success');
                } else {
                    $this->WSModel->insertFriendRequest($fromUser, $toUser);
                    Utils::displayMessage("Usuário {$toUser} não está online, solicitação salva no banco de dados", 'info');
                }
                break;

            case 'responserequest':
                $toConnection = $this->getUserConnection($fromUser);

                if ($response === 'accepted') {
                    Utils::displayMessage("Aceitando solicitação de amizade de {$fromUser} para {$toUser}", 'info');
                    $this->WSModel->acceptFriendRequest($fromUser, $toUser);

                    if ($toConnection) {
                        $this->sendFriendRequestResponse($toConnection, $fromUser, $toUser, 'aceita');
                    }
                } else {
                    Utils::displayMessage("Recusando solicitação de amizade de {$fromUser} para {$toUser}", 'info');
                    if ($toConnection) {
                        $this->sendFriendRequestResponse($toConnection, $fromUser, $toUser, 'rejeitada');
                    }
                }
                break;
        }
    }

    private function getUserConnection($userId)
    {
        $user = array_filter($this->wsController->users, fn($user) => $user['id_bd'] === $userId);
        return $user ? array_values($user)[0]['connection'] : null;
    }

    private function sendFriendRequestResponse($connection, $fromUser, $toUser, $status = null)
    {
        $status = $status ?? 'enviada';
        
        $connection->send(json_encode([
            'type' => 'friendRequest',
            'fromUser' => $fromUser,
            'toUser' => $toUser,
            'status' => $status
        ]));
    }
}
