<?php

namespace api\Websocket;

use Ratchet\MessageComponentInterface;
use Ratchet\ConnectionInterface;

class ChatHandler implements MessageComponentInterface
{
    private $clients = [];
    private $rooms = []; // Keep track of users in rooms

    public function onOpen(ConnectionInterface $conn)
    {
        $this->clients[$conn->resourceId] = $conn;
    }

    public function onMessage(ConnectionInterface $from, $msg)
    {
        $data = json_decode($msg);

        // Handle different message types (chat, friend requests)
        if ($data->type === 'chat') {
            $this->sendToRoom($data->room, $data->message, $from);
        } else if ($data->type === 'friendRequest') {
            // Implement logic to send friend request notification
        }
    }

    public function onClose(ConnectionInterface $conn)
    {
        unset($this->clients[$conn->resourceId]);
    }

    public function onError(ConnectionInterface $conn, \Exception $e)
    {
        // Handle errors
    }

    private function sendToRoom($room, $message, ConnectionInterface $from)
    {
        foreach ($this->clients as $client) {
            if (isset($this->rooms[$client->resourceId]) && $this->rooms[$client->resourceId] === $room) {

                if ($client !== $from) {
                    $client->send(json_encode(['message' => $message]));
                }
            }
        }
    }
}
