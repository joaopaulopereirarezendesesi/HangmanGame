<?php

namespace models;

use tools\Utils;
use Exception;

class FriendsModel
{
    private $utils;

    public function __construct()
    {
        $this->utils = new Utils();
    }

    public function getFriendsById($id) 
{
    echo gettype($id);
    try {
       
        $query = "
            SELECT u.* 
            FROM users u 
            JOIN friends f ON u.ID_U = f.ID_A 
            WHERE f.ID_U = :id 
            UNION 
            SELECT u.* 
            FROM users u 
            JOIN friends f ON u.ID_U = f.ID_U 
            WHERE f.ID_A = :id2;
        ";

        $params = [
            ':id' => (int)$id, 
            ':id2' => (int)$id
        ];

        $result = $this->utils->executeQuery($query, $params, true);

        return $result ?? [];
    } catch (Exception $e) {
        throw new Exception("Erro ao obter amigos do usuário: " . $e->getMessage());
    }
}

}
