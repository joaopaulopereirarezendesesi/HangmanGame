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
    // Declaração das variáveis de modelo
    private $roomModel;
    private $playedModel;

    // Construtor da classe, inicializa os modelos de RoomModel e PlayedModel
    public function __construct()
    {
        $this->roomModel = new RoomModel();  // Instancia o modelo de sala
        $this->playedModel = new PlayedModel(); // Instancia o modelo de jogadores em salas
    }

    // Método para criar uma sala
    public function createRoom()
    {
        // Obtém o ID do organizador da sala a partir do token
        $id = Utils::getUserIdFromToken();
        if (!$id)
            return; // Retorna se não for possível obter o ID do organizador

        // Valida os parâmetros recebidos via POST
        $data = $_POST;

        if (!is_array($data)) {
            Utils::errorResponse('ID do usuário não encontrado ou inválido.', 400);
            return;
        }

        $id_o = (string) $id; // Define o ID do organizador
        $points = isset($_POST['points']) ? (int) $_POST['points'] : 2000; // Define os pontos, padrão 2000
        $room_name = $_POST['room_name'] ?? $this->generateRoomName(); // Define o nome da sala ou gera um
        $private = filter_var($_POST['private'] ?? false, FILTER_VALIDATE_BOOLEAN); // Verifica se a sala é privada

        // Verifica se o nome da sala já existe
        if ($this->roomModel->doesRoomNameExist($room_name)) {
            Utils::errorResponse('Nome de sala já em uso. Escolha outro.');
            return;
        }

        $password = $_POST['password'] ?? ''; // Define a senha para salas privadas

        // Verifica se a senha é obrigatória para salas privadas
        if ($private && empty($password)) {
            Utils::errorResponse('Senha obrigatória para salas privadas.');
            return;
        }

        // Verifica a capacidade de jogadores e o limite de tempo
        $player_capacity = isset($_POST['player_capacity']) ? (int) $_POST['player_capacity'] : 10;
        $time_limit = isset($_POST['time_limit']) ? (int) $_POST['time_limit'] : 5;

        if ($player_capacity < 2 || $time_limit < 2) {
            Utils::errorResponse('Capacidade de jogadores ou tempo limite inválidos.');
            return;
        }

        // Cria a sala usando o modelo
        $result = $this->roomModel->createRoom($id_o, $room_name, $private, $password, $player_capacity, $time_limit, $points);
        $roomId = $result;

        // Retorna os dados da sala criada como resposta JSON
        Utils::jsonResponse([
            'idsala' => $roomId,
            'id_o' => $id_o,
            'nomesala' => $room_name,
            'privacao' => $private,
            'capacidade' => $player_capacity,
            'tampodasala' => $time_limit,
            'pontos' => $points
        ]);
    }

    // Método para gerar o nome da sala baseado no horário atual
    private function generateRoomName()
    {
        $time_day = $this->roomModel->getCurrentRoomTime(); // Obtém o tempo atual da sala
        return 'room_' . $time_day; // Retorna o nome da sala baseado no horário
    }

    // Método para um usuário entrar em uma sala
    public function joinRoom()
    {
        $id = Utils::getUserIdFromToken();
        if (!$id)
            return;

        $data = $_POST;

        // Obtém os dados da sala a partir do ID
        $room = $this->roomModel->getRoomById($data['roomid']);

        if (!$room) {
            Utils::errorResponse('Sala não encontrada.');
            return;
        }

        // Verifica se a sala é privada e valida a senha
        if ($room['PRIVATE'] && !$this->validateRoomPassword($room['PASSWORD'], $data['password'])) {
            Utils::errorResponse('Senha inválida.');
            return;
        }

        // Verifica se a capacidade da sala foi atingida
        if ($this->playedModel->getPlayersCountInRoom($data['roomid']) >= $room['PLAYER_CAPACITY']) {
            Utils::errorResponse('Sala cheia.');
            return;
        }

        // Adiciona o jogador à sala
        $this->playedModel->joinRoom($id, $data['roomId']);

        // Responde com sucesso
        Utils::jsonResponse(['message' => 'Entrou na sala com sucesso.']);
    }

    // Método para remover um jogador de uma sala
    public function removePlayerFromRoom($roomId, $userId)
    {
        $JWT = Utils::getUserIdFromToken();
        if (!$JWT)
            return;

        $room = $this->roomModel->getRoomById($roomId);
        if (!$room) {
            Utils::errorResponse('Sala não encontrada.');
            return;
        }

        // Remove o jogador da sala
        $this->playedModel->leaveRoom($userId, $roomId);

        // Responde com sucesso
        Utils::jsonResponse(['message' => 'Jogador removido com sucesso.']);
    }

    // Método para validar a senha da sala
    function validateRoomPassword($hashedPassword, $password)
    {
        return !empty($password) && password_verify($password, $hashedPassword);
    }

    // Método para obter todas as salas
    public function getRooms()
    {
        try {
            $JWT = Utils::getUserIdFromToken();
            if (!$JWT)
                return;

            // Obtém todas as salas
            $getrooms = $this->roomModel->getRooms();
            if ($getrooms) {
                Utils::jsonResponse(["rooms" => $getrooms], 200); // Retorna as salas em formato JSON
            } else {
                Utils::jsonResponse(["message" => "Nenhuma sala encontrada"], 404); // Caso não haja salas
            }
        } catch (Exception $e) {
            Utils::jsonResponse(["error" => $e->getMessage()], 500); // Captura erros e retorna resposta de erro
        }
    }

    // Método para contar o número de jogadores em uma sala
    public function countPlayers()
    {
        // Conta os jogadores na sala com base no ID fornecido
        $countPlayers = $this->playedModel->countPlayersInRoom((int)$_POST["id"]);
        Utils::jsonResponse(["players" => $countPlayers], 200); // Retorna o número de jogadores
    }
}
