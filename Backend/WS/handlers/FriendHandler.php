<?php

namespace handler;

use Controller\WebSocketController;
use Ratchet\ConnectionInterface;
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

    public function handle(ConnectionInterface $from, $fromUser, $toUser, $actionRequest, $response = null)
    {
        switch ($actionRequest) {
            case 'friendrequest':
                Utils::displayMessage("Processing friend request from {$fromUser} to {$toUser}", 'info');

                $result = $this->WSModel->sendFriendRequest($fromUser, $toUser);
                $status = $result ? 'sent' : 'already exists';
                if ($status === 'already exists') {
                    $this->sendFriendRequestResponse($actionRequest, $from, $fromUser, $toUser, $status);
                    Utils::displayMessage("Friend request already exists", 'info');
                    break;
                } else { 
                    Utils::displayMessage("Friend request sent", 'info');
                    $this->sendFriendRequestResponse($actionRequest, $from, $fromUser, $toUser, $status);
                }

                $toConnection = $this->getUserConnection($toUser);

                if ($toConnection) {
                    $this->sendFriendRequestResponse($actionRequest, $toConnection, $fromUser, $toUser, 'received');
                    Utils::displayMessage("Request sent to {$toUser}", 'success');
                } else {
                    $this->WSModel->insertFriendRequest($fromUser, $toUser);
                    Utils::displayMessage("User {$toUser} is not online, request saved in the database", 'info');
                }
                break;

            case 'responserequest':
                $toConnection = $this->getUserConnection($fromUser);

                if ($response === 'accepted') {
                    Utils::displayMessage("Accepting friend request from {$fromUser} to {$toUser}", 'info');
                    $this->WSModel->acceptFriendRequest($fromUser, $toUser);

                    if ($toConnection) {
                        $this->sendFriendRequestResponse($actionRequest, $toConnection, $fromUser, $toUser, 'accepted');
                    }
                } else {
                    Utils::displayMessage("Rejecting friend request from {$fromUser} to {$toUser}", 'info');
                    if ($toConnection) {
                        $this->sendFriendRequestResponse($actionRequest, $toConnection, $fromUser, $toUser, 'rejected');
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

    private function sendFriendRequestResponse($type, $connection, $fromUser, $toUser, $status = null)
    {
        $connection->send(json_encode([
            'type' => $type,
            'fromUser' => $fromUser,
            'toUser' => $toUser,
            'status' => $status
        ]));
    }
}
