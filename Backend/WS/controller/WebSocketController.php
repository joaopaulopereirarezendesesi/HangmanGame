<?php

namespace controller;

use Ratchet\MessageComponentInterface;
use Ratchet\ConnectionInterface;
use handler\ChatHandler;
use handler\RoomHandler;
use handler\FriendHandler;
//use handler\ReconnectHandler;
use tools\Utils;
use models\WSModel;

class WebSocketController implements MessageComponentInterface
{
    private ChatHandler $chatHandler;
    private RoomHandler $roomHandler;
    private FriendHandler $friendHandler;
    private WSModel $WSModel;
    //private ReconnectHandler $reconnectHandler;

    public array $clients = [];
    public array $rooms = [];
    public array $users = []; 

    public function __construct()
    {
        $this->chatHandler = new ChatHandler($this);
        $this->roomHandler = new RoomHandler($this);
        $this->friendHandler = new FriendHandler($this);
        $this->WSModel = new WSModel();
        //$this->reconnectHandler = new ReconnectHandler($this);
    }

    public function onOpen(ConnectionInterface $conn)
    {
        Utils::displayMessage("Novo cliente conectado: {$conn->resourceId}", 'player_join');
        $this->clients[$conn->resourceId] = [
            "conn" => $conn,
            "authenticated" => false
        ];
    }

    public function onClose(ConnectionInterface $conn)
    {
        Utils::displayMessage("Cliente desconectado: {$conn->resourceId}", 'player_leave');
        unset($this->clients[$conn->resourceId], $this->rooms[$conn->resourceId], $this->users[$conn->resourceId]);
        if (isset($this->users[$conn->resourceId])) {
            $id_bd = $this->users[$conn->resourceId];
            $this->WSModel->changeStatus($id_bd, false); 
        }
    }
    
    public function onMessage(ConnectionInterface $from, $msg)
    {
        $data = json_decode($msg);

        if (!$data) {
            Utils::displayMessage("Dados inválidos recebidos: {$msg}", 'error');
            $from->send(json_encode(['error' => 'Dados inválidos']));
            return;
        }

        if (!isset($data->type)) {
            Utils::displayMessage("Dados incompletos recebidos", 'error');
            $from->send(json_encode(['error' => 'Tipo de mensagem não especificado']));
            return;
        }

        switch ($data->type) {
            case 'login':
                $this->chatHandler->handle($from, $data->id_bd, $data->password);
                break;

            case 'chat':
                $this->chatHandler->handle($from, $data);
                break;

            case 'joinRoom':
                $this->roomHandler->handle($from, (string) $data->room);
                break;

            // case 'reconnect':
            //     $this->reconnectHandler->handle($from, $data);
            //     break;

            case 'friendRequest':
                $this->friendHandler->handle($data->fromUser, $data->toUser);
                break;

            default:
                Utils::displayMessage("Tipo inválido: {$data->type}", 'error');
                $from->send(json_encode(['error' => 'Tipo de mensagem inválido']));
        }
    }

    public function onError(ConnectionInterface $conn, \Exception $e)
    {
        Utils::displayMessage("Erro com o cliente {$conn->resourceId}: " . $e->getMessage(), 'error');
        $conn->send(json_encode(['error' => $e->getMessage()]));
        $conn->close();
    }
}
