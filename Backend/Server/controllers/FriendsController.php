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
            $userId = Utils::getUserIdFromToken();
            if (!$userId) {
                Utils::jsonResponse(["error" => "Token not provided"], 403);
            }

            $friends = $this->friendsModel->getFriendsById(
                strval($userId)
            );

            Utils::jsonResponse(["friends" => $friends]);
        } catch (Exception $e) {
            Utils::debug_log(
                ["controllerErrorFriends-getFriendsById" => $e->getMessage()],
                "error"
            );
            Utils::jsonResponse(["error" => "Internal server error"], 500);
        }
    }
}
