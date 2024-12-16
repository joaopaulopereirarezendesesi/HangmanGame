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
        print_r("Novo cliente conectado: " . $conn->resourceId . "\n");
        print_r($conn->httpRequest->getHeaders());
        
        $this->clients[$conn->resourceId] = $conn;
    }

    public function onMessage(ConnectionInterface $from, $msg)
    {
        print_r("Mensagem recebida de " . $from->resourceId . ": $msg\n");

        $data = json_decode($msg);

        if (isset($data->type)) {
            switch ($data->type) {
                case 'chat':
                    print_r("Enviando mensagem para a sala {$data->room}\n");  
                    $this->sendToRoom($data->room, $data->message, $data->user, $from);
                    break;
                case 'joinRoom':
                    print_r("Cliente {$from->resourceId} entrando na sala {$data->room}\n");
                    $this->joinRoom($from, $data->room);
                    break;
                case 'friendRequest':
                    print_r("Solicitação de amizade de {$data->fromUser} para {$data->toUser}\n");
                    $this->handleFriendRequest($data->fromUser, $data->toUser);
                    break;
                default:
                    $from->send(json_encode(['error' => 'Tipo de mensagem inválido']));
            }
        } else {
            print_r("Dados inválidos recebidos: " . print_r($data, true) . "\n");
        }
    }

    public function onClose(ConnectionInterface $conn)
    {
        print_r("Cliente desconectado: {$conn->resourceId}\n");

        unset($this->clients[$conn->resourceId]);
        unset($this->rooms[$conn->resourceId]);
    }

    public function onError(ConnectionInterface $conn, \Exception $e)
    {
        // Exibindo o erro ocorrido
        print_r("Erro com o cliente {$conn->resourceId}: " . $e->getMessage() . "\n");

        $conn->send(json_encode(['error' => $e->getMessage()]));
        $conn->close();
    }

    private function joinRoom(ConnectionInterface $conn, $room)
    {
        $this->rooms[$conn->resourceId] = $room;
        print_r("Cliente {$conn->resourceId} entrou na sala {$room}\n");
        $conn->send(json_encode(['success' => "Entrou na sala {$room}"]));
    }

    private function sendToRoom($roomId, $message, $user, ConnectionInterface $from)
    {
        print_r("Enviando mensagem para a sala {$roomId}: $message\n");

        $players = $this->WSModel->getUsersInRoom($roomId);
        print_r("Usuários na sala {$roomId}: " . print_r($players, true) . "\n");

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
        print_r("Processando a solicitação de amizade de {$fromUser} para {$toUser}\n");
        
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
        print_r("Atualização na sala {$roomId}: {$action} por {$userId}\n");

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
