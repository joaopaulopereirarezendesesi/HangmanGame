<?php

require_once __DIR__ . '/../models/RoomModel.php';
require_once __DIR__ . '/../models/PlayedModel.php';

class RoomController
{
    private $roomModel;
    private $playedModel;

    public function __construct()
    {
        $this->roomModel = new RoomModel();
        $this->playedModel = new PlayedModel();
    }

    public function createRoom()
    {
        if (!empty($_POST['id'])) {
            $id_o = $_POST['id'];

            $room_name = $_POST['room_name'] ?? $this->generateRoomName();

            if ($this->roomModel->doesRoomNameExist($room_name)) {
                echo json_encode(['error' => 'Nome de sala já em uso. Escolha outro.']);
                return;
            }

            $private = isset($_POST['private']) ? (bool)$_POST['private'] : false;

            if ($private && empty($_POST['password'])) {
                echo json_encode(['error' => 'Senha obrigatória para salas privadas']);
                return; 
            }

            $password = $private ? $_POST['password'] : null;
            $player_capacity = isset($_POST['player_capacity']) ? (int)$_POST['player_capacity'] : 10;
            $time_limit = isset($_POST['time_limit']) ? (int)$_POST['time_limit'] : 5;

            if ($player_capacity < 1 || $time_limit < 1) {
                echo json_encode(['error' => 'Capacidade de jogadores ou tempo limite inválidos']);
                return;
            }

            $result = $this->roomModel->createRoom($id_o, $room_name, $private, $password, $player_capacity, $time_limit);
            echo json_encode($result);
        } else {
            echo json_encode(['error' => 'ID do organizador não informado']);
        }
    }

    private function generateRoomName()
    {
        $time_day = $this->roomModel->getCurrentRoomTime();
        return 'room_' . $time_day;
    }

    public function joinRoom($roomId, $userId, $password)
    {
        $room = $this->roomModel->getRoomById($roomId);

        if (!$room) {
            echo json_encode(['error' => 'Sala não encontrada']);
            return;
        }

        if ($room['PRIVATE'] && (empty($password) || $password !== $room['PASSWORD'])) {
            echo json_encode(['error' => 'Senha inválida']);
            return;
        }

        $currentPlayers = $this->playedModel->getPlayersCountInRoom($roomId);

        if ($currentPlayers >= $room['PLAYER_CAPACITY']) {
            echo json_encode(['error' => 'Sala cheia']);
            return;
        }

        $this->playedModel->joinRoom($userId, $roomId);

        echo json_encode(['message' => 'Entrou na sala com sucesso']);
    }

    public function removePlayerFromRoom($roomId, $userId)
    {
        $room = $this->roomModel->getRoomById($roomId);
        if (!$room) {
            echo json_encode(['error' => 'Sala não encontrada']);
            return;
        }

        $this->playedModel->leaveRoom($userId, $roomId);

        echo json_encode(['message' => 'Jogador removido com sucesso']);
    }
}
