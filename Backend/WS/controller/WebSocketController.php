<?php

namespace controller;

use Ratchet\MessageComponentInterface;
use Ratchet\ConnectionInterface;
use handler\ChatHandler;
use handler\RoomHandler;
use handler\FriendHandler;
use handler\LoginHandler;
//use handler\ReconnectHandler;
use tools\Utils;
use models\WSModel;

class WebSocketController implements MessageComponentInterface
{
    private ChatHandler $chatHandler;
    private RoomHandler $roomHandler;
    private FriendHandler $friendHandler;
    private LoginHandler $LoginHandler;
    private WSModel $WSModel;
    //private ReconnectHandler $reconnectHandler;

    public array $clients = [];
    public array $rooms = [];
    public array $users = []; 

    public function __construct()
    {
        $this->LoginHandler = new LoginHandler($this);
        $this->chatHandler = new ChatHandler($this);
        $this->roomHandler = new RoomHandler($this);
        $this->friendHandler = new FriendHandler($this);
        $this->WSModel = new WSModel();
        //$this->reconnectHandler = new ReconnectHandler($this);
    }

    public function onOpen(ConnectionInterface $conn)
    {
        Utils::displayMessage("New client connected: {$conn->resourceId}", 'player_join');
        $this->clients[$conn->resourceId] = [
            "conn" => $conn,
            "authenticated" => false
        ];
    }

    public function onClose(ConnectionInterface $conn)
    {
        Utils::displayMessage("Client disconnected: {$conn->resourceId}", 'player_leave');
        unset($this->clients[$conn->resourceId], $this->rooms[$conn->resourceId], $this->users[$conn->resourceId]);
        if (isset($this->users[$conn->resourceId])) {
            $id_bd = $this->users[$conn->resourceId];
            $this->WSModel->changeStatus($id_bd, false); 
        }
    }
    
    public function onMessage(ConnectionInterface $from, $msg)
    {
        $data = json_decode($msg);
        $id = $from->resourceId;

        if (!$this->clients[$id]['authenticated'] && $data->type !== 'login') {
            Utils::displayMessage("Unauthenticated client tried to send {$data->type}", 'error');
            $from->send(json_encode(['error' => 'Authentication required']));
            return;
        }

        if (!$data) {
            Utils::displayMessage("Invalid data received: {$msg}", 'error');
            $from->send(json_encode(['error' => 'Invalid data']));
            return;
        }

        if (!isset($data->type)) {
            Utils::displayMessage($data, 'error');
            Utils::displayMessage("Incomplete data received", 'error');
            $from->send(json_encode(['error' => 'Message type not specified']));
            return;
        }

        switch ($data->type) {
            case 'login':
                $this->LoginHandler->handle($from, $data->id_bd, $data->password);
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
                $this->friendHandler->handle($from, $data->fromUser, $data->toUser, $data->actionRequest, $data->response);
                break;

            default:
                Utils::displayMessage("Invalid type: {$data->type}", 'error');
                $from->send(json_encode(['error' => 'Invalid message type']));
        }
    }

    public function onError(ConnectionInterface $conn, \Exception $e)
    {
        Utils::displayMessage("Error with client {$conn->resourceId}: " . $e->getMessage(), 'error');
        $conn->send(json_encode(['error' => $e->getMessage()]));
        $conn->close();
    }
}
