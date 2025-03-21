<?php

namespace controllers;

require_once __DIR__ . '/../vendor/autoload.php';

use models\RoomModel;
use models\PlayedModel;
use tools\Utils;
use core\JwtHandler;
use Exception;

class RoomController
{
    private $roomModel;
    private $playedModel;

    public function __construct()
    {
        $this->roomModel = new RoomModel();
        $this->playedModel = new PlayedModel();
    }

    private function getUserIdFromToken()
    {
        $token = Utils::getToken();
        if (!$token) {
            Utils::errorResponse('Token não encontrado.', 401);
            return null;
        }

        $decoded = JwtHandler::validateToken($token);
        if (!$decoded)
            return null;

        return $decoded['user_id'];
    }

    public function createRoom()
    {
        $id_o = $this->getUserIdFromToken();
        if (!$id_o)
            return;

        $data = Utils::validateParams($_POST, ['id']);

        if (!is_array($data) || !isset($data['id'])) {
            Utils::errorResponse('ID do usuário não encontrado ou inválido.', 400);
            return;
        }

        $id_o = (int) $data['id'];
        $points = isset($_POST['points']) ? (int) $_POST['points'] : 2000;
        $room_name = $_POST['room_name'] ?? $this->generateRoomName();
        $private = filter_var($_POST['private'] ?? false, FILTER_VALIDATE_BOOLEAN);

        if ($this->roomModel->doesRoomNameExist($room_name)) {
            Utils::errorResponse('Nome de sala já em uso. Escolha outro.');
            return;
        }

        $password = $_POST['password'] ?? '';

        if ($private && empty($password)) {
            Utils::errorResponse('Senha obrigatória para salas privadas.');
            return;
        }

        $player_capacity = isset($_POST['player_capacity']) ? (int) $_POST['player_capacity'] : 10;
        $time_limit = isset($_POST['time_limit']) ? (int) $_POST['time_limit'] : 5;

        if ($player_capacity < 2 || $time_limit < 2) {
            Utils::errorResponse('Capacidade de jogadores ou tempo limite inválidos.');
            return;
        }

        Utils::debug_log(json_encode([
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

        Utils::jsonResponse([
            'idsala' => $roomId,
            'id_o' => $id_o,
            'nomesala' => $room_name,
            'privacao' => $private,
            'capacidade' => $player_capacity,
            'tampodasala' => $time_limit,
            'pontos' => $points
        ]);

        Utils::jsonResponse("ALOWW");

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
        if (!$JWT)
            return;

        $room = $this->roomModel->getRoomById($roomId);

        if (!$room) {
            Utils::errorResponse('Sala não encontrada.');
            return;
        }

        if ($room['PRIVATE'] && !$this->validateRoomPassword($room['PASSWORD'], $password)) {
            Utils::errorResponse('Senha inválida.');
            return;
        }

        if ($this->playedModel->getPlayersCountInRoom($roomId) >= $room['PLAYER_CAPACITY']) {
            Utils::errorResponse('Sala cheia.');
            return;
        }

        $this->playedModel->joinRoom($userId, $roomId);

        Utils::jsonResponse(['message' => 'Entrou na sala com sucesso.']);
    }

    public function removePlayerFromRoom($roomId, $userId)
    {
        $room = $this->roomModel->getRoomById($roomId);

        if (!$room) {
            Utils::errorResponse('Sala não encontrada.');
            return;
        }

        $this->playedModel->leaveRoom($userId, $roomId);

        Utils::jsonResponse(['message' => 'Jogador removido com sucesso.']);
    }

    function validateRoomPassword($hashedPassword, $password)
    {
        return !empty($password) && password_verify($password, $hashedPassword);
    }

    public function getRooms()
    {
        try {
            $getrooms = $this->roomModel->getRooms();
            if ($getrooms) {
                Utils::jsonResponse(["rooms" => $getrooms], 200);
            } else {
                Utils::jsonResponse(["message" => "Nenhuma sala encontrada"], 404);
            }
        } catch (Exception $e) {
            Utils::jsonResponse(["error" => $e->getMessage()], 500);
        }
    }


    public function countPlayers()
    {
        $countPlayers = $this->playedModel->countPlayersInRoom((int)$_POST["id"]);
        Utils::jsonResponse(["players" => $countPlayers], 200);
    }
}
