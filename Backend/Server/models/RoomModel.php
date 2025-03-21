<?php

namespace models;

use tools\Utils;
use Exception;
use DateTime;
use DateTimeZone;

class RoomModel
{
    // Instância de utilitário para executar queries no banco de dados
    private $utils;

    // Construtor da classe, inicializa a instância de Utils
    public function __construct()
    {
        $this->utils = new Utils();
    }

    // Método para obter o horário atual da sala em formato UTC
    public function getCurrentRoomTime()
    {
        // Cria um objeto DateTime com a data e hora atuais no fuso horário UTC
        $dateTime = new DateTime('now', new DateTimeZone('UTC'));
        // Retorna a data e hora no formato 'Y-m-d H:i:s.u' (ano-mês-dia hora:minuto:segundo.microsegundo)
        return $dateTime->format('Y-m-d H:i:s.u');
    }

    // Método para criar uma nova sala no banco de dados
    public function createRoom($id_o, $room_name, $private, $password, $player_capacity, $time_limit, $points)
    {
        try {
            // Define a query SQL para inserir uma nova sala na tabela "rooms"
            $query = "INSERT INTO rooms (ID_R, ID_O, ROOM_NAME, PRIVATE, PASSWORD, PLAYER_CAPACITY, TIME_LIMIT, POINTS) 
                    VALUES (UUID(), :id_o, :room_name, :private, :password, :player_capacity, :time_limit, :points)";

            // Parâmetros para substituir os valores na query SQL
            $params = [
                ':id_o' => $id_o,
                ':room_name' => $room_name,
                ':private' => $private,
                ':password' => $private ? password_hash($password, PASSWORD_ARGON2ID) : null, // Se a sala for privada, faz o hash da senha
                ':player_capacity' => $player_capacity,
                ':time_limit' => $time_limit,
                ':points' => $points
            ];

            // Executa a query para inserir os dados da sala
            $this->utils->executeQuery($query, $params);

            // Após a criação da sala, obtém o ID da sala recém-criada pelo nome
            return $this->getRoomNameId($room_name);
        } catch (Exception $e) {
            // Caso ocorra um erro, lança uma exceção com a mensagem de erro
            throw new Exception("Erro ao criar sala: " . $e->getMessage());
        }
    }

    // Método para obter o ID de uma sala com base no nome da sala
    public function getRoomNameId($roomName)
    {
        try {
            // Query para buscar o ID da sala pelo nome
            $query = "SELECT ID_R FROM rooms WHERE ROOM_NAME = :roomName";
            $params = [':roomName' => $roomName];

            // Executa a query e retorna o ID da sala
            $result = $this->utils->executeQuery($query, $params, true);

            return $result[0]['ID_R'] ?? null; // Retorna o ID ou null se não encontrado
        } catch (Exception $e) {
            // Caso ocorra um erro, lança uma exceção com a mensagem de erro
            throw new Exception("Erro ao obter o ID da sala: " . $e->getMessage());
        }
    }

    // Método para obter informações de uma sala pelo seu ID
    public function getRoomById($roomId)
    {
        try {
            // Query para buscar todos os dados da sala pelo ID
            $query = "SELECT * FROM rooms WHERE ID_R = :roomId";
            $params = [':roomId' => $roomId];

            // Executa a query e retorna os dados da sala
            $result = $this->utils->executeQuery($query, $params, true);

            return $result[0] ?? null; // Retorna os dados da sala ou null se não encontrado
        } catch (Exception $e) {
            // Caso ocorra um erro, lança uma exceção com a mensagem de erro
            throw new Exception("Erro ao obter sala: " . $e->getMessage());
        }
    }

    // Método para verificar se o nome de uma sala já existe no banco de dados
    public function doesRoomNameExist($roomName)
    {
        try {
            // Query para contar quantas vezes o nome da sala aparece na tabela "rooms"
            $query = "SELECT COUNT(*) FROM rooms WHERE ROOM_NAME = :roomName";
            $params = [':roomName' => $roomName];

            // Executa a query e obtém o resultado
            $result = $this->utils->executeQuery($query, $params, true);

            return $result[0]['COUNT(*)'] > 0; // Retorna true se o nome da sala já existir, false caso contrário
        } catch (Exception $e) {
            // Caso ocorra um erro, lança uma exceção com a mensagem de erro
            throw new Exception("Erro ao verificar nome da sala: " . $e->getMessage());
        }
    }

    // Método para obter todas as salas no banco de dados
    public function getRooms()
    {
        try {
            // Query para buscar todas as salas da tabela "rooms"
            $query = "SELECT * FROM rooms";

            // Executa a query e retorna todas as salas
            $result = $this->utils->executeQuery($query, [], true);

            return $result ?? null; // Retorna todas as salas ou null se não houver salas
        } catch (Exception $e) {
            // Caso ocorra um erro, lança uma exceção com a mensagem de erro
            throw new Exception("Erro ao obter sala: " . $e->getMessage());
        }
    }
}
