<?php

namespace models;

use core\Database;
use PDO;
use PDOException;
use Exception;

class PlayedModel
{
    private $db;

    public function __construct()
    {
        $this->db = Database::connect();
    }

    public function getPlayersCountInRoom($roomId)
    {
        try {
            $query = "SELECT COUNT(*) AS player_count FROM played WHERE ID_R = :roomId";
            $stmt = $this->db->prepare($query);
            $stmt->bindParam(':roomId', $roomId, PDO::PARAM_INT);
            $stmt->execute();
            $row = $stmt->fetch(PDO::FETCH_ASSOC);

            return $row['player_count'] ?? 0;
        } catch (PDOException $e) {
            throw new Exception("Erro ao contar jogadores na sala: " . $e->getMessage());
        }
    }

    public function joinRoom($userId, $roomId)                                                                                                                                                             
    {
        try {
            $query = "INSERT INTO played (ID_U, ID_R, SCORE, IS_THE_CHALLENGER) VALUES (:userId, :roomId, 0, 0)";
            $stmt = $this->db->prepare($query);
            $stmt->execute([':userId' => $userId, ':roomId' => $roomId]);
        } catch (PDOException $e) {
            throw new Exception("Erro ao entrar na sala: " . $e->getMessage());
        }
    }

    public function leaveRoom($userId, $roomId)
    {
        try {
            $query = "DELETE FROM played WHERE ID_U = :userId AND ID_R = :roomId";
            $stmt = $this->db->prepare($query);
            $stmt->execute([':userId' => $userId, ':roomId' => $roomId]);
        } catch (PDOException $e) {
            throw new Exception("Erro ao sair da sala: " . $e->getMessage());
        }
    }
}
