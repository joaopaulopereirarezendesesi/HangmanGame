<?php

require_once __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . '/../tools/helpers.php';
require_once __DIR__ . '/../core/JwtHandler.php';

class FriendsController
{
    private $friendsModel;

    public function __construct()
    {
        $this->friendsModel = new \models\FriendsModel();
    }

    public function getFriendsById() {
        try {
            $data = json_decode(file_get_contents('php://input'), true);
            
            if (isset($data['id'])) {
                $friends = $this->friendsModel->getFriendsById((int)$data['id']);
                \tools\Utils::jsonResponse([
                    'friends' => $friends
                ], 200);
            } else {
                throw new Exception("ID não fornecido.");
            }
        } catch (Exception $e) {
            \tools\Utils::jsonResponse([
                'error' => $e->getMessage()
            ], 500);
        }
    }
    
    
}
