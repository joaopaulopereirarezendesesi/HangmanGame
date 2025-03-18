<?php

namespace controllers;

require_once __DIR__ . '/../vendor/autoload.php';

use models\FriendsModel;
use tools\Utils;
use Exception;

class FriendsController
{
    private $friendsModel;

    public function __construct()
    {
        $this->friendsModel = new FriendsModel();
    }

    public function getFriendsById() {
        try {
            $data = json_decode(file_get_contents('php://input'), true);
            
            if (isset($data['id'])) {
                $friends = $this->friendsModel->getFriendsById((int)$data['id']);
                Utils::jsonResponse([
                    'friends' => $friends
                ], 200);
            } else {
                throw new Exception("ID não fornecido.");
            }
        } catch (Exception $e) {
            Utils::jsonResponse([
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
