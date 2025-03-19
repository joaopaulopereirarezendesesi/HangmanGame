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

    public function verifiedIdPassword($id)
    {
        try {
            $query = "SELECT PASSWORD FROM users WHERE ID_U = :id";
            $params = [':id' => $id];
    
            $result = $this->utils->executeQuery($query, $params, true);
            
            if ($result && isset($result[0]['PASSWORD'])) {
                return $result[0]['PASSWORD'];
            }
    
            return false; 
        } catch (Exception $e) {
            return false;
        }
    }

    public function insertFriendRequest($id_r, $id_d)
    {
        try {
            $query = "INSERT INTO `friend_requests` (UUID(), `sender_id`, `receiver_id`) VALUES (:id_r, :id_d)";
            $params = [
                ':id_r' => $id_r,
                ':id_d' => $id_d
            ];

            $this->utils->executeQuery($query, $params);
    
            return true; 
        } catch (Exception $e) {
            return false;
        }
    }

    public function acceptFriendRequest($id_r, $id_d)
    {
        try {
            $query = "INSERT INTO `friends` (`ID_U`, `ID_A`) VALUES (:id_r, :id_d), (:id_d, :id_r);";
            $params = [
                ':id_r' => $id_r,
                ':id_d' => $id_d
            ];

            $this->utils->executeQuery($query, $params);
    
            return true; 
        } catch (Exception $e) {
            return false;
        }
    }
}

