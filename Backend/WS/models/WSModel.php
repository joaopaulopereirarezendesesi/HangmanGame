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

    public function changeStatus($fromUser, bool $status)
    {
        try {
            $query = "UPDATE `users` SET `ONLINE` = :status WHERE `ID_U` = :fromUser;";
            $params = [
                ':fromUser' => $fromUser,
                ':status' => $status
            ];
    
            $this->utils->executeQuery($query, $params);
    
            return true;
        } catch (Exception $e) {
            return false;
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
