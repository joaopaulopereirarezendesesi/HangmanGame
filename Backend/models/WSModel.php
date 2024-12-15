<?php

namespace models;

use Database;
use PDO;
use PDOException;

class WSModel
{
    private $db;

    public function __construct()
    {
        $this->db = Database::connect();
    }

    public function getUsersInRoom($roomId)
    {
        try {
            $query = "SELECT u.ID_U, u.NICKNAME
                      FROM users u
                      JOIN played p ON u.ID_U = p.ID_U
                      WHERE p.ID_R = :roomId";
            $stmt = $this->db->prepare($query);
            $stmt->bindParam(':roomId', $roomId, PDO::PARAM_INT);
            $stmt->execute();
            $users = $stmt->fetchAll(PDO::FETCH_ASSOC);

            return $users;
        } catch (PDOException $e) {
            throw new \Exception("Erro ao obter usuários na sala: " . $e->getMessage());
        }
    }

    public function sendFriendRequest($fromUser, $toUser)
    {
        try {
            $query = "INSERT INTO friends (from_user, to_user, status) VALUES (:fromUser, :toUser, 'pending')";
            $stmt = $this->db->prepare($query);
            $stmt->bindParam(':fromUser', $fromUser, PDO::PARAM_INT);
            $stmt->bindParam(':toUser', $toUser, PDO::PARAM_INT);
            $stmt->execute();
            return true;
        } catch (PDOException $e) {
            return false;
        }
    }
}