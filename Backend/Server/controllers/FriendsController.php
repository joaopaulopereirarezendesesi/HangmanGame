<?php

namespace controllers;

require_once __DIR__ . "/../vendor/autoload.php";

use models\FriendsModel;
use tools\Utils;
use Exception;

class FriendsController
{
    private FriendsModel $friendsModel;

    /**
     * Initializes the friends model instance.
     */
    public function __construct()
    {
        $this->friendsModel = new FriendsModel();
    }

    /**
     * Retrieves the authenticated user's friends list and returns it as JSON.
     */
    public function getFriendsById(): void
    {
        try {
            $id = Utils::getUserIdFromToken();
            if (!$id) {
                return;
            }

            $friends = $this->friendsModel->getFriendsById(
                filter_var($id, FILTER_SANITIZE_STRING)
            );

            Utils::jsonResponse(["friends" => $friends], 200);
        } catch (Exception $e) {
            Utils::jsonResponse(["error" => $e->getMessage()], 500);
        }
    }
}
