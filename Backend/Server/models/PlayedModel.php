<?php

namespace models;

use tools\Utils;
use Exception;

class PlayedModel
{
    private $utils;

    public function __construct()
    {
        $this->utils = new Utils();
    }

    public function getPlayersCountInRoom($roomId)
    {
        try {
            $query = "SELECT COUNT(*) AS player_count FROM played WHERE ID_R = :roomId";
            $params = [':roomId' => $roomId];
            $result = $this->utils->executeQuery($query, $params, true);

            return $result[0]['player_count'] ?? 0;
        } catch (Exception $e) {
            throw new Exception("Erro ao contar jogadores na sala: " . $e->getMessage());
        }
    }

    public function joinRoom($userId, $roomId)
    {
        try {
            $query = "INSERT INTO played (ID_U, ID_R, SCORE, IS_THE_CHALLENGER) VALUES (:userId, :roomId, 0, 0)";
            $params = [':userId' => $userId, ':roomId' => $roomId];
            $this->utils->executeQuery($query, $params);
        } catch (Exception $e) {
            throw new Exception("Erro ao entrar na sala: " . $e->getMessage());
        }
    }

    public function leaveRoom($userId, $roomId)
    {
        try {
            $query = "DELETE FROM played WHERE ID_U = :userId AND ID_R = :roomId";
            $params = [':userId' => $userId, ':roomId' => $roomId];
            $this->utils->executeQuery($query, $params);
        } catch (Exception $e) {
            throw new Exception("Erro ao sair da sala: " . $e->getMessage());
        }
    }
    
    public function countPlayersInRoom($roomId = 1)
    {
        try {
            $query = "SELECT COUNT(*) as player_count FROM played WHERE ID_R = :roomId";
            $params = [':roomId' => $roomId];
            $result = $this->utils->executeQuery($query, $params, true);
    
            return $result[0]['player_count'] ?? 0;
        } catch (Exception $e) {
            return "Erro: " . $e->getMessage();
        }
    }
}
