<?php

namespace models;

use tools\Utils;
use Exception;

class FriendsModel
{
    private Utils $utils;

    /**
     * Constructor of the class. Instantiates the Utils object for later use.
     */
    public function __construct()
    {
        $this->utils = new Utils();
    }

    /**
     * Retrieves the list of friends of a user based on their ID.
     *
     * @param int $id The ID of the user whose friends will be retrieved
     * @return array The list of the user's friends
     * @throws Exception In case of error when executing the query
     */
    public function getFriendsById(string $userId): array
    {
        try {
            $query = "
            SELECT u.PHOTO, u.NICKNAME, u.ONLINE
            FROM users u
            JOIN friends f ON (u.ID_U = f.ID_A OR u.ID_U = f.ID_U)
            WHERE f.ID_U = :id OR f.ID_A = :id;
        ";

            $params = [
                ":id" => $userId,
            ];
            $result = $this->utils->executeQuery($query, $params, true);

            return $result ?? [];
        } catch (Exception $e) {
            Utils::debug_log(
                [
                    "modelsErrorFriends-getFriendsById" => $e->getMessage(),
                ],
                "error"
            );
            Utils::jsonResponse(["error" => "Internal server error"], 500);
            exit();
        }
    }
}
