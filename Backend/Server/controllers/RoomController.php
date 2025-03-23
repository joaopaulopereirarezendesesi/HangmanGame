<?php

namespace controllers;

// Carrega as dependências do projeto
require_once __DIR__ . '/../vendor/autoload.php';

// Importa os modelos e utilitários necessários
use models\RoomModel;   // Modelo para manipulação de salas
use models\PlayedModel; // Modelo para manipulação de jogadores nas salas
use tools\Utils;        // Classe de utilitários
use Exception;          // Classe para manipulação de exceções

class RoomController
{
    /** @var RoomModel Instância do modelo de salas */
    private $roomModel;
    
    /** @var PlayedModel Instância do modelo de jogadores em salas */
    private $playedModel;

    /**
     * Construtor da classe, inicializa os modelos RoomModel e PlayedModel
     */
    public function __construct()
    {
        $this->roomModel = new RoomModel();
        $this->playedModel = new PlayedModel();
    }

    /**
     * Método para criar uma nova sala
     *
     * @return void
     */
    public function createRoom()
    {
        // Obtém o ID do usuário a partir do token de autenticação
        $id = Utils::getUserIdFromToken();
        if (!$id) return;

        $data = $_POST;

        // Verifica se os dados recebidos são válidos
        if (!is_array($data)) {
            Utils::errorResponse('ID do usuário não encontrado ou inválido.', 400);
            return;
        }

        // Define valores padrão para os parâmetros da sala
        $id_o = (string) $id;
        $points = isset($_POST['points']) ? (int) $_POST['points'] : 2000;
        $room_name = $_POST['room_name'] ?? $this->generateRoomName();
        $private = filter_var($_POST['private'] ?? false, FILTER_VALIDATE_BOOLEAN);

        // Verifica se o nome da sala já existe
        if ($this->roomModel->doesRoomNameExist($room_name)) {
            Utils::errorResponse('Nome de sala já em uso. Escolha outro.');
            return;
        }

        // Valida senha para salas privadas
        $password = $_POST['password'] ?? '';
        if ($private && empty($password)) {
            Utils::errorResponse('Senha obrigatória para salas privadas.');
            return;
        }

        // Define capacidade de jogadores e tempo limite da sala
        $player_capacity = isset($_POST['player_capacity']) ? (int) $_POST['player_capacity'] : 10;
        $time_limit = isset($_POST['time_limit']) ? (int) $_POST['time_limit'] : 5;

        // Valida valores mínimos de capacidade e tempo limite
        if ($player_capacity < 2 || $time_limit < 2) {
            Utils::errorResponse('Capacidade de jogadores ou tempo limite inválidos.');
            return;
        }

        // Cria a sala no banco de dados
        $result = $this->roomModel->createRoom($id_o, $room_name, $private, $password, $player_capacity, $time_limit, $points);
        
        // Retorna os detalhes da sala criada
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
     * Método para gerar um nome de sala baseado no horário atual
     *
     * @return string Nome gerado para a sala
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
        // Obtém o ID do usuário autenticado
        $id = Utils::getUserIdFromToken();
        if (!$id) return;

        $data = $_POST;
        $room = $this->roomModel->getRoomById($data['roomid']);

        // Verifica se a sala existe
        if (!$room) {
            Utils::errorResponse('Sala não encontrada.');
            return;
        }

        // Valida a senha da sala caso seja privada
        if ($room['PRIVATE'] && !$this->validateRoomPassword($room['PASSWORD'], $data['password'])) {
            Utils::errorResponse('Senha inválida.');
            return;
        }

        // Verifica se a sala já atingiu a capacidade máxima de jogadores
        if ($this->playedModel->getPlayersCountInRoom($data['roomid']) >= $room['PLAYER_CAPACITY']) {
            Utils::errorResponse('Sala cheia.');
            return;
        }

        // Adiciona o jogador à sala
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
        // Obtém o ID do usuário autenticado
        $JWT = Utils::getUserIdFromToken();
        if (!$JWT) return;

        // Verifica se a sala existe
        $room = $this->roomModel->getRoomById($roomId);
        if (!$room) {
            Utils::errorResponse('Sala não encontrada.');
            return;
        }

        // Remove o jogador da sala
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
            // Obtém o ID do usuário autenticado
            $JWT = Utils::getUserIdFromToken();
            if (!$JWT) return;

            // Busca as salas disponíveis no banco de dados
            $getrooms = $this->roomModel->getRooms();
            if ($getrooms) {
                Utils::jsonResponse(["rooms" => $getrooms], 200);
            } else {
                Utils::jsonResponse(["message" => "Nenhuma sala encontrada"], 404);
            }
        } catch (Exception $e) {
            // Trata possíveis erros na consulta das salas
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
        // Obtém o número de jogadores na sala especificada
        $countPlayers = $this->playedModel->countPlayersInRoom((int)$_POST["id"]);
        Utils::jsonResponse(["players" => $countPlayers], 200);
    }
}
