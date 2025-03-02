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
    
            $result = $this->utils->executeQuery($query, $params);
            
            if ($result && isset($result[0]['PASSWORD'])) {
                return $result[0]['PASSWORD'];
            }
    
            return false; 
        } catch (Exception $e) {
            return false;
        }
    }
    

}

