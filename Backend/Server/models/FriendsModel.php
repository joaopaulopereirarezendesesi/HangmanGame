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

    public function getFriendsById(string $userId): array
    {
        try {
            $queryFriends = "
            SELECT 
                u.PHOTO AS foto,
                u.NICKNAME AS name,
                u.ONLINE AS status,
                r.POSITION AS rank
            FROM ranking r
            JOIN users u ON r.ID_U = u.ID_U
            WHERE r.ID_U IN (
                SELECT CASE 
                    WHEN f.ID_U = :id THEN f.ID_A
                    ELSE f.ID_U
                END
                FROM friends f
                WHERE f.ID_U = :id1 OR f.ID_A = :id2
            )
            ORDER BY r.POSITION ASC;
            ";

            $queryTotalPlayers = "
            SELECT COUNT(*) AS total_players
            FROM ranking;
            ";

            $params = [
                ":id" => $userId,
                ":id1" => $userId,
                ":id2" => $userId,
            ];

            // Executando as duas consultas
            $resultFriends = $this->utils->executeQuery($queryFriends, $params, true);
            $resultTotalPlayers = $this->utils->executeQuery($queryTotalPlayers, [], true);

            // Obtendo o total de jogadores do ranking
            $totalPlayers = $resultTotalPlayers[0]['total_players'] ?? 0;

            // Estrutura final com os amigos e o total de jogadores
            $response = [
                "total_players" => $totalPlayers, // total de jogadores
                "friends" => $resultFriends,      // amigos
            ];

            return $response;
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
