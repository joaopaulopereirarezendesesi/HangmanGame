<?php

namespace api\Websocket;

use Ratchet\MessageComponentInterface;
use Ratchet\ConnectionInterface;
use models\WSModel;

class WShandler implements MessageComponentInterface
{
    private $clients = [];
    private $rooms = [];
    private $WSModel;

    public function __construct()
    {
        $this->WSModel = new WSModel();
    }

    public function onOpen(ConnectionInterface $conn)
    {
        $this->clients[$conn->resourceId] = $conn;
    }

    public function onMessage(ConnectionInterface $from, $msg)
    {
        $data = json_decode($msg);

        if (isset($data->type)) {
            switch ($data->type) {
                case 'chat':
                    $this->sendToRoom($data->room, $data->message, $data->user, $from);
                    break;
                case 'joinRoom':
                    $this->joinRoom($from, $data->room);
                    break;
                case 'friendRequest':
                    $this->handleFriendRequest($data->fromUser, $data->toUser);
                    break;
                default:
                    $from->send(json_encode(['error' => 'Tipo de mensagem inválido']));
            }
        }
    }

    public function onClose(ConnectionInterface $conn)
    {
        unset($this->clients[$conn->resourceId]);
        unset($this->rooms[$conn->resourceId]);
    }

    public function onError(ConnectionInterface $conn, \Exception $e)
    {
        $conn->send(json_encode(['error' => $e->getMessage()]));
        $conn->close();
    }

    private function joinRoom(ConnectionInterface $conn, $room)
    {
        $this->rooms[$conn->resourceId] = $room;
        $conn->send(json_encode(['success' => "Entrou na sala {$room}"]));
    }

    private function sendToRoom($roomId, $message, $user, ConnectionInterface $from)
    {
        $players = $this->WSModel->getUsersInRoom($roomId);
        
        foreach ($this->clients as $resourceId => $client) {
            if (isset($this->rooms[$resourceId]) && $this->rooms[$resourceId] == $roomId && $client !== $from) {
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
        $result = $this->WSModel->sendFriendRequest($fromUser, $toUser);

        if ($result) {
            foreach ($this->clients as $client) {
                $client->send(json_encode([ 
                    'type' => 'friendRequest',
                    'fromUser' => $fromUser,
                    'toUser' => $toUser,
                    'status' => 'enviada'
                ]));
            }
        } else {
            foreach ($this->clients as $client) {
                $client->send(json_encode([ 
                    'type' => 'friendRequest',
                    'fromUser' => $fromUser,
                    'toUser' => $toUser,
                    'status' => 'já existe'
                ]));
            }
        }
    }

    public function broadcastRoomUpdate($roomId, $userId, $action)
    {
        foreach ($this->clients as $client) {
            if (isset($this->rooms[$client->resourceId]) && $this->rooms[$client->resourceId] == $roomId) {
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
