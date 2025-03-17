<?php

require_once __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . '/../tools/helpers.php';
require_once __DIR__ . '/../core/JwtHandler.php';

class RoomController
{
    private $roomModel;
    private $playedModel;

    public function __construct()
    {
        $this->roomModel = new \models\RoomModel();
        $this->playedModel = new \models\PlayedModel();
    }

    private function getUserIdFromToken()
    {
        $token = \tools\Utils::getToken();
        if (!$token) {
            \tools\Utils::errorResponse('Token não encontrado.', 401);
            return null;
        }

        $decoded = \core\JwtHandler::validateToken($token);
        if (!$decoded) return null;

        return $decoded['user_id'];
    }

    public function createRoom()
    {
        $id_o = $this->getUserIdFromToken();
        if (!$id_o) return;
        
        $data = \tools\Utils::validateParams($_POST, ['id']);
        
        if (!is_array($data) || !isset($data['id'])) {
            \tools\Utils::errorResponse('ID do usuário não encontrado ou inválido.', 400);
            return;
        }
        
        $id_o = (int) $data['id'];
        $points = isset($_POST['points']) ? (int) $_POST['points'] : 2000;
        $room_name = $_POST['room_name'] ?? $this->generateRoomName();
        $private = filter_var($_POST['private'] ?? false, FILTER_VALIDATE_BOOLEAN);
        
        if ($this->roomModel->doesRoomNameExist($room_name)) {
            \tools\Utils::errorResponse('Nome de sala já em uso. Escolha outro.');
            return;
        }
        
        $password = $_POST['password'] ?? '';
        
        if ($private && empty($password)) {
            \tools\Utils::errorResponse('Senha obrigatória para salas privadas.');
            return;
        }
        
        $player_capacity = isset($_POST['player_capacity']) ? (int) $_POST['player_capacity'] : 10;
        $time_limit = isset($_POST['time_limit']) ? (int) $_POST['time_limit'] : 5;
        
        if ($player_capacity < 2 || $time_limit < 2) {
            \tools\Utils::errorResponse('Capacidade de jogadores ou tempo limite inválidos.');
            return;
        }
        

        \tools\Utils::debug_log(json_encode([
            'id_o' => $id_o,
            'points' => $points,
            'room_name' => $room_name,
            'private' => $private,
            'password' => $password ?? 'N/A',
            'player_capacity' => $player_capacity ?? 'N/A',
            'time_limit' => $time_limit ?? 'N/A'
        ], JSON_PRETTY_PRINT));

        $result = $this->roomModel->createRoom($id_o, $room_name, $private, $password, $player_capacity, $time_limit, $points);
        $roomId = $result;
        
        \tools\Utils::jsonResponse([
            'idsala' => $roomId,
            'id_o' => $id_o,
            'nomesala' => $room_name,
            'privacao' => $private,
            'capacidade' => $player_capacity,
            'tampodasala' => $time_limit,
            'pontos' => $points
        ]);

        $this->joinRoom($roomId, $id_o, $password);
    }

    private function generateRoomName()
    {
        $time_day = $this->roomModel->getCurrentRoomTime();
        return 'room_' . $time_day;
    }

    public function joinRoom($roomId, $userId, $password = null)
    {
        $JWT = $this->getUserIdFromToken();
        if (!$JWT) return;

        $room = $this->roomModel->getRoomById($roomId);

        if (!$room) {
            \tools\Utils::errorResponse('Sala não encontrada.');
            return;
        }

        if ($room['PRIVATE'] && !$this->validateRoomPassword($room['PASSWORD'], $password)) {
            \tools\Utils::errorResponse('Senha inválida.');
            return;
        }

        if ($this->playedModel->getPlayersCountInRoom($roomId) >= $room['PLAYER_CAPACITY']) {
            \tools\Utils::errorResponse('Sala cheia.');
            return;
        }

        $this->playedModel->joinRoom($userId, $roomId);

        \tools\Utils::jsonResponse(['message' => 'Entrou na sala com sucesso.']);
    }

    public function removePlayerFromRoom($roomId, $userId)
    {
        $room = $this->roomModel->getRoomById($roomId);

        if (!$room) {
            \tools\Utils::errorResponse('Sala não encontrada.');
            return;
        }

        $this->playedModel->leaveRoom($userId, $roomId);

        \tools\Utils::jsonResponse(['message' => 'Jogador removido com sucesso.']);
    }

    function validateRoomPassword($hashedPassword, $password)
    {
        return !empty($password) && password_verify($password, $hashedPassword);
    }

    public function getRooms()
    {
        \tools\Utils::jsonResponse([
            'rooms' =>  $this->roomModel->getRooms()
        ]);
    }

    
}
