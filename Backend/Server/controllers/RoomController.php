<?php

namespace controllers;

// Carrega as dependências
require_once __DIR__ . '/../vendor/autoload.php';

use models\RoomModel;   // Importa o modelo de salas
use models\PlayedModel; // Importa o modelo de jogadores em salas
use tools\Utils;        // Importa as funções auxiliares da classe Utils
use Exception;          // Importa a classe Exception para captura de erros

class RoomController
{
    /** @var RoomModel */
    private $roomModel;
    
    /** @var PlayedModel */
    private $playedModel;

    /**
     * Construtor da classe, inicializa os modelos de RoomModel e PlayedModel
     */
    public function __construct()
    {
        $this->roomModel = new RoomModel();
        $this->playedModel = new PlayedModel();
    }

    /**
     * Método para criar uma sala
     *
     * @return void
     */
    public function createRoom()
    {
        $id = Utils::getUserIdFromToken();
        if (!$id) return;

        $data = $_POST;

        if (!is_array($data)) {
            Utils::errorResponse('ID do usuário não encontrado ou inválido.', 400);
            return;
        }

        $id_o = (string) $id;
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

        $result = $this->roomModel->createRoom($id_o, $room_name, $private, $password, $player_capacity, $time_limit, $points);
        
        Utils::jsonResponse([
            'idsala' => $result,
            'id_o' => $id_o,
            'nomesala' => $room_name,
            'privacao' => $private,
            'capacidade' => $player_capacity,
            'tampodasala' => $time_limit,
            'pontos' => $points
        ]);
    }

    /**
     * Método para gerar o nome da sala baseado no horário atual
     *
     * @return string Nome gerado da sala
     */
    private function generateRoomName()
    {
        $time_day = $this->roomModel->getCurrentRoomTime();
        return 'room_' . $time_day;
    }

    /**
     * Método para um usuário entrar em uma sala
     *
     * @return void
     */
    public function joinRoom()
    {
        $id = Utils::getUserIdFromToken();
        if (!$id) return;

        $data = $_POST;
        $room = $this->roomModel->getRoomById($data['roomid']);

        if (!$room) {
            Utils::errorResponse('Sala não encontrada.');
            return;
        }

        if ($room['PRIVATE'] && !$this->validateRoomPassword($room['PASSWORD'], $data['password'])) {
            Utils::errorResponse('Senha inválida.');
            return;
        }

        if ($this->playedModel->getPlayersCountInRoom($data['roomid']) >= $room['PLAYER_CAPACITY']) {
            Utils::errorResponse('Sala cheia.');
            return;
        }

        $this->playedModel->joinRoom($id, $data['roomId']);
        Utils::jsonResponse(['message' => 'Entrou na sala com sucesso.']);
    }

    /**
     * Método para remover um jogador de uma sala
     *
     * @param int $roomId ID da sala
     * @param int $userId ID do usuário a ser removido
     * @return void
     */
    public function removePlayerFromRoom($roomId, $userId)
    {
        $JWT = Utils::getUserIdFromToken();
        if (!$JWT) return;

        $room = $this->roomModel->getRoomById($roomId);
        if (!$room) {
            Utils::errorResponse('Sala não encontrada.');
            return;
        }

        $this->playedModel->leaveRoom($userId, $roomId);
        Utils::jsonResponse(['message' => 'Jogador removido com sucesso.']);
    }

    /**
     * Método para validar a senha da sala
     *
     * @param string $hashedPassword Senha criptografada da sala
     * @param string $password Senha fornecida pelo usuário
     * @return bool True se a senha estiver correta, false caso contrário
     */
    function validateRoomPassword($hashedPassword, $password)
    {
        return !empty($password) && password_verify($password, $hashedPassword);
    }

    /**
     * Método para obter todas as salas disponíveis
     *
     * @return void
     */
    public function getRooms()
    {
        try {
            $JWT = Utils::getUserIdFromToken();
            if (!$JWT) return;

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

    /**
     * Método para contar o número de jogadores em uma sala
     *
     * @return void
     */
    public function countPlayers()
    {
        $countPlayers = $this->playedModel->countPlayersInRoom((int)$_POST["id"]);
        Utils::jsonResponse(["players" => $countPlayers], 200);
    }
}
