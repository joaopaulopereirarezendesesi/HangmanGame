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
        echo "Novo cliente conectado: " . $conn->resourceId . "\n";
        echo $conn->httpRequest->getHeaders();
        
        $this->clients[$conn->resourceId] = $conn;
    }

    public function onMessage(ConnectionInterface $from, $msg)
    {
        echo "Mensagem recebida de " . $from->resourceId . ": $msg\n";

        $data = json_decode($msg);

        if (isset($data->type)) {
            switch ($data->type) {
                case 'chat':
                    echo "Enviando mensagem para a sala {$data->room}\n";  
                    $this->sendToRoom($data->room, $data->message, $data->user, $from);
                    break;
                case 'joinRoom':
                    echo "Cliente {$from->resourceId} entrando na sala {$data->room}\n";
                    $this->joinRoom($from, $data->room);
                    break;
                case 'friendRequest':
                    echo "Solicitação de amizade de {$data->fromUser} para {$data->toUser}\n";
                    $this->handleFriendRequest($data->fromUser, $data->toUser);
                    break;
                default:
                    $from->send(json_encode(['error' => 'Tipo de mensagem inválido']));
            }
        } else {
            echo "Dados inválidos recebidos: " . print_r($data, true) . "\n";
        }
    }

    public function onClose(ConnectionInterface $conn)
    {
        echo "Cliente desconectado: {$conn->resourceId}\n";

        unset($this->clients[$conn->resourceId]);
        unset($this->rooms[$conn->resourceId]);
    }

    public function onError(ConnectionInterface $conn, \Exception $e)
    {
        echo "Erro com o cliente {$conn->resourceId}: " . $e->getMessage() . "\n";

        $conn->send(json_encode(['error' => $e->getMessage()]));
        $conn->close();
    }

    private function joinRoom(ConnectionInterface $conn, $room)
    {
        $this->rooms[$conn->resourceId] = $room;
        echo "Cliente {$conn->resourceId} entrou na sala {$room}\n";
        $conn->send(json_encode(['success' => "Entrou na sala {$room}"]));
    }

    private function sendToRoom($roomId, $message, $user, ConnectionInterface $from)
    {
        echo "Enviando mensagem para a sala {$roomId}: $message\n";

        $players = $this->WSModel->getUsersInRoom($roomId);
        echo "Usuários na sala {$roomId}: " . print_r($players, true) . "\n";

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
        echo "Processando a solicitação de amizade de {$fromUser} para {$toUser}\n";
        
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
        echo "Atualização na sala {$roomId}: {$action} por {$userId}\n";

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
