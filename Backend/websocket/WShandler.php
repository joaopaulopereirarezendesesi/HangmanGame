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
    }

    public function onOpen(ConnectionInterface $conn)
    {
        Utils::displayMessage("Novo cliente conectado: {$conn->resourceId}", 'success');
        $this->clients[$conn->resourceId] = $conn;
    }

    public function onMessage(ConnectionInterface $from, $msg)
    {
        $data = json_decode($msg);

        if (!$data) {
            Utils::displayMessage("Dados inválidos recebidos: {$msg}", 'error');
            $from->send(json_encode(['error' => 'Dados inválidos']));
            return;
        }

        if (isset($data->type)) {
            switch ($data->type) {
                case 'chat':
                    Utils::displayMessage("Enviando mensagem para a sala {$data->room}", 'info');
                    $this->sendToRoom($data->room, $data->message, $data->user, $from);
                    break;

                case 'joinRoom':
                    Utils::displayMessage("Cliente {$from->resourceId} entrando na sala {$data->room}", 'info');
                    $this->joinRoom($from, $data->room);
                    break;

                case 'friendRequest':
                    Utils::displayMessage("Solicitação de amizade de {$data->fromUser} para {$data->toUser}", 'info');
                    $this->handleFriendRequest($data->fromUser, $data->toUser);
                    break;

                default:
                    Utils::displayMessage("Tipo de mensagem inválido: {$data->type}", 'error');
                    $from->send(json_encode(['error' => 'Tipo de mensagem inválido']));
            }
        } else {
            Utils::errorResponse("Dados incompletos recebidos", 400);
        }
    }

    public function onClose(ConnectionInterface $conn)
    {
        Utils::displayMessage("Cliente desconectado: {$conn->resourceId}", 'info');
        unset($this->clients[$conn->resourceId]);
        unset($this->rooms[$conn->resourceId]);
    }

    public function onError(ConnectionInterface $conn, \Exception $e)
    {
        Utils::displayMessage("Erro com o cliente {$conn->resourceId}: " . $e->getMessage(), 'error');
        $conn->send(json_encode(['error' => $e->getMessage()]));
        $conn->close();
    }

    private function joinRoom(ConnectionInterface $conn, $room)
    {
        $this->rooms[$conn->resourceId] = $room;
        Utils::displayMessage("Cliente {$conn->resourceId} entrou na sala {$room}", 'success');
        $conn->send(json_encode(['success' => "Entrou na sala {$room}"]));
        Utils::displayMessage("array rooms: " . print_r($this->rooms, true), 'success');
    }

    private function sendToRoom($roomId, $message, $user, ConnectionInterface $from)
    {
        Utils::displayMessage("Enviando mensagem para a sala {$roomId}: {$message}", 'info');

        $players = $this->WSModel->getUsersInRoom($roomId);

        if (!$players) {
            Utils::displayMessage("Nenhum usuário na sala {$roomId}", 'error');
            $from->send(json_encode(['error' => "Nenhum usuário na sala {$roomId}"]));
            return;
        }

        foreach ($this->clients as $resourceId => $client) {
            if (isset($this->rooms[$resourceId]) && $this->rooms[$resourceId] === $roomId && $client !== $from) {
                $client->send(json_encode([
                    'type' => 'chat',
                    'room' => $roomId,
                    'user' => $user,
                    'message' => $message,
                ]));
            }
        }
    }

    private function handleFriendRequest($fromUser, $toUser)
    {
        Utils::displayMessage("Processando a solicitação de amizade de {$fromUser} para {$toUser}", 'info');

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

    public function broadcastRoomUpdate($roomId, $userId, $action)
    {
        Utils::displayMessage("Atualização na sala {$roomId}: {$action} por {$userId}", 'info');

        foreach ($this->clients as $client) {
            if (isset($this->rooms[$client->resourceId]) && $this->rooms[$client->resourceId] === $roomId) {
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
