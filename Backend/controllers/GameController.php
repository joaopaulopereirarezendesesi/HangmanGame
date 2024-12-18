<?php

require_once __DIR__ . '/../vendor/autoload.php';

class GameController
{
    private $gameModel;
    private $wsHandler;

    public function __construct()
    {
        $this->gameModel = new \models\GameModel();  
        $this->wsHandler = new \Websocket\WShandler();
    }
}