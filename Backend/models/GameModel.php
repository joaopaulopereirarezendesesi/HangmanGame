<?php

class GameModel
{
    private $db;

    public function __construct()
    {
        $this->db = Database::connect();  
    }
}
