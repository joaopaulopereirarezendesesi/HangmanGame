<?php

namespace handler;

use Controller\WebSocketController;
use Ratchet\ConnectionInterface;
use tools\Utils;

class ChatHandler
{
    private WebSocketController $wsController;

    public function __construct(WebSocketController $wsController)
    {
        $this->wsController = $wsController;
    }

    public function handle(ConnectionInterface $from, $data)
    {
        if (!isset($data->room, $data->message, $data->user)) {
            Utils::displayMessage("Dados incompletos para envio de mensagem.", 'error');
            $from->send(json_encode(['error' => 'Dados incompletos']));
            return;
        }
        $this->sendToRoom($data->room, $data->message, $data->user, $from);
    }

    private function sendToRoom(string $roomId, string $message, string $user, ConnectionInterface $from): void
    {
        Utils::displayMessage("Enviando mensagem para a sala {$roomId}: {$message}", 'info');

        foreach ($this->wsController->clients as $resourceId => $client) {
            if (($this->wsController->rooms[$resourceId] ?? null) === $roomId && $client !== $from) {
                $client->send(json_encode([
                    'type' => 'chat',
                    'room' => $roomId,
                    'user' => $user,
                    'message' => $message,
                ]));
            }
        }
    }
}
