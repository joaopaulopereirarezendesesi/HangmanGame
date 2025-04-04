<?php

namespace models;

use tools\Utils;
use Exception;

class PlayedModel
{
    private Utils $utils;

    /**
     * Class constructor. Instantiates the Utils object to perform database operations.
     */
    public function __construct()
    {
        $this->utils = new Utils();
    }

    /**
     * Returns the number of players in a room.
     *
     * @param string $roomId The ID of the room to be checked
     * @return ?int The number of players in the room
     * @throws Exception If an error occurs while executing the database query
     */
    public function getPlayersCountInRoom(string $roomId): ?int
    {
        try {
            $playerCountQuery =
                "SELECT COUNT(*) AS player_count FROM played WHERE ID_R = :roomId";
            $queryParameters = [":roomId" => $roomId];

            $playerCountResult = $this->utils->executeQuery($playerCountQuery, $queryParameters, true);

            return $playerCountResult[0]["player_count"] ?? null;
        } catch (Exception $e) {
            Utils::debug_log(
                [
                    "modelsErrorPlayed-getPlayersCountInRoom" => $e->getMessage(),
                ],
                "error"
            );
            Utils::jsonResponse(["error" => "Internal server error"], 500);

            return null;
        }
    }

    /**
     * Adds a player to a room.
     *
     * @param string $userId The ID of the user to be added
     * @param string $roomId The ID of the room where the user will be added
     * @throws Exception If an error occurs while executing the database query
     */
    public function joinRoom(string $userId, string $roomId): void
    {
        try {
            $joinRoomQuery =
                "INSERT INTO played (ID_PLAYED, ID_U, ID_R, SCORE, IS_THE_CHALLENGER) VALUES (UUID(), :userId, :roomId, 0, 0)";
            $queryParameters = [":userId" => $userId, ":roomId" => $roomId];

            $this->utils->executeQuery($joinRoomQuery, $queryParameters);
        } catch (Exception $e) {
            Utils::debug_log(
                [
                    "modelsErrorPlayed-joinRoom" => $e->getMessage(),
                ],
                "error"
            );
            Utils::jsonResponse(["error" => "Internal server error"], 500);
        }
    }

    /**
     * Removes a player from a room.
     *
     * @param string $userId The ID of the user to be removed
     * @param string $roomId The ID of the room from which the player will be removed
     * @throws Exception If an error occurs while executing the database query
     */
    public function leaveRoom(string $userId, string $roomId): void
    {
        try {
            $leaveRoomQuery =
                "DELETE FROM played WHERE ID_U = :userId AND ID_R = :roomId";
            $queryParameters = [":userId" => $userId, ":roomId" => $roomId];

            $this->utils->executeQuery($leaveRoomQuery, $queryParameters);
        } catch (Exception $e) {
            Utils::debug_log(
                [
                    "modelsErrorPlayed-leaveRoom" => $e->getMessage(),
                ],
                "error"
            );
            Utils::jsonResponse(["error" => "Internal server error"], 500);
        }
    }

    /**
     * Counts the number of players in a room (additional function with a name similar to getPlayersCountInRoom).
     *
     * @param string $roomId The ID of the room to be checked (default value is 1)
     * @return ?int The number of players in the room
     */
    public function countPlayersInRoom(string $roomId): ?int
    {
        try {
            $countPlayersQuery =
                "SELECT COUNT(*) as player_count FROM played WHERE ID_R = :roomId";
            $queryParameters = [":roomId" => $roomId];

            $countPlayersResult = $this->utils->executeQuery($countPlayersQuery, $queryParameters, true);

            return $countPlayersResult[0]["player_count"] ?? 0;
        } catch (Exception $e) {
            Utils::debug_log(
                [
                    "modelsErrorPlayed-countPlayersInRoom" => $e->getMessage(),
                ],
                "error"
            );
            Utils::jsonResponse(["error" => "Internal server error"], 500);

            return null;
        }
    }
}
