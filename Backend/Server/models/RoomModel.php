<?php

namespace models;

use tools\Utils;
use Exception;

class RoomModel
{
    private Utils $utils;

    public function __construct()
    {
        $this->utils = new Utils();
    }

    public function createRoom(
        string $organizerId,
        string $roomName,
        bool $isPrivate,
        string $roomPassword,
        int $playerCapacity,
        int $timeLimit,
        int $points,
        string $modality,
        string $modalityImage
    ): ?string {
        try {
            $createRoomQuery = "INSERT INTO rooms (ID_R, ID_O, ROOM_NAME, PRIVATE, PASSWORD, PLAYER_CAPACITY, TIME_LIMIT, POINTS, MODALITY, MODALITY_IMG) 
                    VALUES (UUID(), :organizerId, :roomName, :isPrivate, :roomPassword, :playerCapacity, :timeLimit, :points, :modality, :modalityImage)";

            $queryParameters = [
                ":organizerId" => $organizerId,
                ":roomName" => $roomName,
                ":isPrivate" => $isPrivate,
                ":roomPassword" => $isPrivate
                    ? password_hash($roomPassword, PASSWORD_ARGON2ID)
                    : null,
                ":playerCapacity" => $playerCapacity,
                ":timeLimit" => $timeLimit,
                ":points" => $points,
                ":modality" => $modality,
                ":modalityImage" => $modalityImage,
            ];

            $this->utils->executeQuery($createRoomQuery, $queryParameters);

            return $this->getRoomIdByName($roomName);
        } catch (Exception $e) {
            Utils::debug_log(
                [
                    "modelsErrorRoom-createRoom" => $e->getMessage(),
                ],
                "error"
            );
            Utils::jsonResponse(["error" => "Internal server error"], 500);

            return null;
        }
    }

    public function getRoomIdByName(string $roomName): ?string
    {
        try {
            $roomIdQuery = "SELECT ID_R FROM rooms WHERE ROOM_NAME = :roomName";
            $queryParameters = [":roomName" => $roomName];

            $roomIdResult = $this->utils->executeQuery($roomIdQuery, $queryParameters, true);

            return $roomIdResult[0]["ID_R"] ?? null;
        } catch (Exception $e) {
            Utils::debug_log(
                [
                    "modelsErrorRoom-getRoomNameId" => $e->getMessage(),
                ],
                "error"
            );
            Utils::jsonResponse(["error" => "Internal server error"], 500);

            return null;
        }
    }

    public function getRoomById(string $roomId): ?array
    {
        try {
            $roomQuery = "SELECT * FROM rooms WHERE ID_R = :roomId";
            $queryParameters = [":roomId" => $roomId];

            $roomResult = $this->utils->executeQuery($roomQuery, $queryParameters, true);

            return $roomResult[0] ?? null;
        } catch (Exception $e) {
            Utils::debug_log(
                [
                    "modelsErrorRoom-getRoomById" => $e->getMessage(),
                ],
                "error"
            );
            Utils::jsonResponse(["error" => "Internal server error"], 500);

            return null;
        }
    }

    public function doesRoomNameExist(string $roomName): ?bool
    {
        try {
            $roomNameExistsQuery = "SELECT COUNT(*) FROM rooms WHERE ROOM_NAME = :roomName";
            $queryParameters = [":roomName" => $roomName];

            $roomNameExistsResult = $this->utils->executeQuery($roomNameExistsQuery, $queryParameters, true);

            return $roomNameExistsResult[0]["COUNT(*)"] > 0;
        } catch (Exception $e) {
            Utils::debug_log(
                [
                    "modelsErrorRoom-doesRoomNameExist" => $e->getMessage(),
                ],
                "error"
            );
            Utils::jsonResponse(["error" => "Internal server error"], 500);

            return null;
        }
    }

    public function getRooms(): ?array
    {
        try {
            $roomsQuery = "SELECT * FROM rooms";

            $roomsResult = $this->utils->executeQuery($roomsQuery, [], true);

            return $roomsResult ?? null;
        } catch (Exception $e) {
            Utils::debug_log(
                [
                    "modelsErrorRoom-getRooms" => $e->getMessage(),
                ],
                "error"
            );
            Utils::jsonResponse(["error" => "Internal server error"], 500);

            return null;
        }
    }
}
