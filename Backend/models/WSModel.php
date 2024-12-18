<?php

namespace models;

use tools\Utils;
use Exception;

class WSModel
{
    private $utils;

    public function __construct()
    {
        $this->utils = new Utils(); 
    }

    public function getUsersInRoom($roomId)
    {
        try {
            $query = "SELECT u.ID_U, u.NICKNAME
                      FROM users u
                      JOIN played p ON u.ID_U = p.ID_U
                      WHERE p.ID_R = :roomId";
            $params = [':roomId' => $roomId];
            $users = $this->utils->executeQuery($query, $params, true);

            return $users;
        } catch (Exception $e) {
            throw new Exception("Erro ao obter usuários na sala: " . $e->getMessage());
        }
    }

    public function getAllRoomsAndUsers()
    {
        try {
            $query = "SELECT ID_U, ID_R FROM played";
            $roomsAndUsers = $this->utils->executeQuery($query, [], true);

            return $roomsAndUsers;
        } catch (Exception $e) {
            throw new Exception("Erro ao restaurar usuários e salas: " . $e->getMessage());
        }
    }

    public function sendFriendRequest($fromUser, $toUser)
    {
        try {
            $query = "INSERT INTO friends (from_user, to_user, status) VALUES (:fromUser, :toUser, 'pending')";
            $params = [
                ':fromUser' => $fromUser,
                ':toUser' => $toUser
            ];

            $this->utils->executeQuery($query, $params); 

            return true;
        } catch (Exception $e) {
            return false;
        }
    }
}
