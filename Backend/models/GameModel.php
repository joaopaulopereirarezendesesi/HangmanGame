<?php

namespace models;

use core\Database;
use PDO;
use PDOException;
use Exception;

class GameModel
{
    private $db;

    public function __construct()
    {
        $this->db = Database::connect();  
    }
}
