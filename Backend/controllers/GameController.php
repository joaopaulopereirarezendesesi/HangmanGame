<?php

require_once __DIR__ . '/../models/GameModel.php';

class GameController
{
    private $gameModel;
    private $wsHandler;

    public function __construct()
    {
        $this->gameModel = new GameModel();  
        $this->wsHandler = new \api\Websocket\WShandler();
    }
}