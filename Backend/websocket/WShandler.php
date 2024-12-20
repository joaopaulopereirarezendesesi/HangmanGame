<?php

namespace Websocket;

use Ratchet\MessageComponentInterface;
use Ratchet\ConnectionInterface;
use models\WSModel;
use tools\Utils;

class WShandler implements MessageComponentInterface
{
    private array $clients = [];
    private array $rooms = [];
    private WSModel $WSModel;

    public function __construct()
    {
        $this->WSModel = new WSModel();
        //$this->reinitializeServerData();
    }

    private function reinitializeServerData()
    {
        $roomsAndUsers = $this->WSModel->getAllRoomsAndUsers();

        if(!empty($roomsAndUsers))
        {
            Utils::errorResponse("type: reconnect", 500);
        }
        return;
    }

    public function onOpen(ConnectionInterface $conn): void
    {
        Utils::displayMessage("Novo cliente conectado: {$conn->resourceId}", 'success');
        $this->clients[$conn->resourceId] = $conn;
    }

    public function onMessage(ConnectionInterface $from, $msg): void
    {
        $data = json_decode($msg);

        if (!$data) {
            Utils::displayMessage("Dados inválidos recebidos: {$msg}", 'error');
            $from->send(json_encode(['error' => 'Dados inválidos']));
            return;
        }

        if (!isset($data->type)) {
            Utils::errorResponse("Dados incompletos recebidos", 400);
            return;
        }

        switch ($data->type) {
            case 'chat':
                $this->handleChatMessage($from, $data);
                break;

            case 'joinRoom':
                $this->joinRoom($from, $data->room);
                break;

            case 'friendRequest':
                $this->handleFriendRequest($data->fromUser, $data->toUser);
                break;

            default:
                $this->handleInvalidMessageType($from, $data->type);
        }
    }

    public function onClose(ConnectionInterface $conn): void
    {
        Utils::displayMessage("Cliente desconectado: {$conn->resourceId}", 'info');
        unset($this->clients[$conn->resourceId], $this->rooms[$conn->resourceId]);
    }

    public function onError(ConnectionInterface $conn, \Exception $e): void
    {
        Utils::displayMessage("Erro com o cliente {$conn->resourceId}: " . $e->getMessage(), 'error');
        $conn->send(json_encode(['error' => $e->getMessage()]));
        $conn->close();
    }

    private function handleChatMessage(ConnectionInterface $from, $data): void
    {
        Utils::displayMessage("Enviando mensagem para a sala {$data->room}", 'info');
        $this->sendToRoom($data->room, $data->message, $data->user, $from);
    }

    private function handleInvalidMessageType(ConnectionInterface $from, string $type): void
    {
        Utils::displayMessage("Tipo de mensagem inválido: {$type}", 'error');
        $from->send(json_encode(['error' => 'Tipo de mensagem inválido']));
    }

    private function joinRoom(ConnectionInterface $conn, string $room): void
    {
        $this->rooms[$conn->resourceId] = $room;
        Utils::displayMessage("Cliente {$conn->resourceId} entrou na sala {$room}", 'success');
        $conn->send(json_encode(['success' => "Entrou na sala {$room}"]));
    }

    private function sendToRoom(string $roomId, string $message, string $user, ConnectionInterface $from): void
    {
        Utils::displayMessage("Enviando mensagem para a sala {$roomId}: {$message}", 'info');

        //$players = $this->WSModel->getUsersInRoom($roomId);

        //if (!$players) {
           ///Utils::displayMessage("Nenhum usuário na sala {$roomId}", 'error');
           //$from->send(json_encode(['error' => "Nenhum usuário na sala {$roomId}"]));
           //return;
        //}

        foreach ($this->clients as $resourceId => $client) {
            if ($this->rooms[$resourceId] === $roomId && $client !== $from) {
                $client->send(json_encode([
                    'type' => 'chat',
                    'room' => $roomId,
                    'user' => $user,
                    'message' => $message,
                ]));
            }
        }
    }

    private function handleFriendRequest(string $fromUser, string $toUser): void
    {
        Utils::displayMessage("Processando solicitação de amizade de {$fromUser} para {$toUser}", 'info');

        $result = $this->WSModel->sendFriendRequest($fromUser, $toUser);
        $status = $result ? 'enviada' : 'já existe';

        foreach ($this->clients as $client) {
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

    public function broadcastRoomUpdate(string $roomId, string $userId, string $action): void
    {
        Utils::displayMessage("Atualização na sala {$roomId}: {$action} por {$userId}", 'info');

        foreach ($this->clients as $client) {
            if ($this->rooms[$client->resourceId] === $roomId) {
                $client->send(json_encode([
                    'type' => 'roomUpdate',
                    'room' => $roomId,
                    'user' => $userId,
                    'action' => $action
                ]));
            }
        }
    }
}
