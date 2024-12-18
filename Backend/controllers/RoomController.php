<?php

require_once __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . '/../tools/helpers.php';

class RoomController
{
    private $roomModel;
    private $playedModel;
    private $wsHandler;

    public function __construct()
    {
        $this->roomModel = new \models\RoomModel();
        $this->playedModel = new \models\PlayedModel();
        $this->wsHandler = new \Websocket\WShandler();
    }

    public function createRoom()
    {
        $data = validateParams($_POST, ['id']); 

        $id_o = $data['id'];
        $points = $_POST['points'] ?? 2000;
        $room_name = $_POST['room_name'] ?? $this->generateRoomName();
        $private = isset($_POST['private']) ? (bool)$_POST['private'] : false;

        if ($this->roomModel->doesRoomNameExist($room_name)) {
            errorResponse('Nome de sala já em uso. Escolha outro.');
        }

        $password = $private ? $_POST['password'] ?? null : null;
        if ($private && empty($password)) {
            errorResponse('Senha obrigatória para salas privadas.');
        }

        $player_capacity = (int)($_POST['player_capacity'] ?? 10);
        $time_limit = (int)($_POST['time_limit'] ?? 5);
        if ($player_capacity < 1 || $time_limit < 1) {
            errorResponse('Capacidade de jogadores ou tempo limite inválidos.');
        }

        $result = $this->roomModel->createRoom($id_o, $room_name, $private, $password, $player_capacity, $time_limit, $points);
        $roomId = $result['idroom'];

        $this->joinRoom($roomId, $id_o, $password);

        jsonResponse([
            'idsala' => $roomId,
            'id_o' => $id_o,
            'nomesala' => $room_name,
            'privacao' => $private,
            'capacidade' => $player_capacity,
            'tampodasala' => $time_limit,
            'pontos' => $points
        ]);
    }

    private function generateRoomName()
    {
        $time_day = $this->roomModel->getCurrentRoomTime();
        return 'room_' . $time_day;
    }

    public function joinRoom($roomId, $userId, $password = null)
    {
        $room = $this->roomModel->getRoomById($roomId);

        if (!$room) {
            errorResponse('Sala não encontrada.');
        }

        if ($room['PRIVATE'] && validateRoomPassword($room['PASSWORD'], $password)) {
            errorResponse('Senha inválida.');
        }

        if ($this->playedModel->getPlayersCountInRoom($roomId) >= $room['PLAYER_CAPACITY']) {
            errorResponse('Sala cheia.');
        }

        $this->playedModel->joinRoom($userId, $roomId);
        $this->wsHandler->broadcastRoomUpdate($roomId, $userId, 'joined');

        jsonResponse(['message' => 'Entrou na sala com sucesso.']);
    }

    public function removePlayerFromRoom($roomId, $userId)
    {
        $room = $this->roomModel->getRoomById($roomId);

        if (!$room) {
            errorResponse('Sala não encontrada.');
        }

        $this->playedModel->leaveRoom($userId, $roomId);
        $this->wsHandler->broadcastRoomUpdate($roomId, $userId, 'left');

        jsonResponse(['message' => 'Jogador removido com sucesso.']);
    }
}