<?php

namespace models;

use tools\Utils;
use Exception;

class FriendsModel
{
    private Utils $utils;

    public function __construct()
    {
        $this->utils = new Utils();
    }

    public function getFriendsById(string $userId): ?array
    {
        try {
            $friendsQuery = "
            SELECT 
                u.PHOTO AS photo,
                u.NICKNAME AS name,
                u.ONLINE AS status,
                r.POSITION AS rank
            FROM ranking r
            JOIN users u ON r.ID_U = u.ID_U
            WHERE r.ID_U IN (
                SELECT CASE 
                    WHEN f.ID_U = :userId THEN f.ID_A
                    ELSE f.ID_U
                END
                FROM friends f
                WHERE f.ID_U = :userId1 OR f.ID_A = :userId2
            )
            ORDER BY r.POSITION ASC;
            ";

            $totalPlayersQuery = "
            SELECT COUNT(*) AS total_players
            FROM ranking;
            ";

            $queryParameters = [
                ":userId" => $userId,
                ":userId1" => $userId,
                ":userId2" => $userId,
            ];

            $friendsResult = $this->utils->executeQuery($friendsQuery, $queryParameters, true);

            if (empty($friendsResult)) {
                return [];
            }

            $totalPlayersResult = $this->utils->executeQuery($totalPlayersQuery, [], true);

            return [
                "total_players" => $totalPlayersResult[0]['total_players'],
                "friends" => $friendsResult,
            ];
        } catch (Exception $e) {
            Utils::debug_log(
                [
                    "modelsErrorFriends-getFriendsById" => $e->getMessage(),
                ],
                "error"
            );
            Utils::jsonResponse(["error" => "Internal server error"], 500);

            return null;
        }
    }
}