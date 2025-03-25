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
        string $id_o,
        string $room_name,
        bool $private,
        string $password,
        int $player_capacity,
        int $time_limit,
        int $points,
        string $modality,
        string $modality_img
    ): string {
        try {
            $query = "INSERT INTO rooms (ID_R, ID_O, ROOM_NAME, PRIVATE, PASSWORD, PLAYER_CAPACITY, TIME_LIMIT, POINTS, MODALITY, MODALITY_IMG) 
                    VALUES (UUID(), :id_o, :room_name, :private, :password, :player_capacity, :time_limit, :points, :modality, :modality_img)";

            $params = [
                ":id_o" => $id_o,
                ":room_name" => $room_name,
                ":private" => $private,
                ":password" => $private
                    ? password_hash($password, PASSWORD_ARGON2ID)
                    : null,
                ":player_capacity" => $player_capacity,
                ":time_limit" => $time_limit,
                ":points" => $points,
                ":modality" => $modality,
                ":modality_img" => $modality_img,
            ];

            $this->utils->executeQuery($query, $params);

            return $this->getRoomNameId($room_name);
        } catch (Exception $e) {
            Utils::debug_log(
                [
                    "modelsErrorRoom-createRoom" => $e->getMessage(),
                ],
                "error"
            );
            Utils::jsonResponse(["error" => "Internal server error"], 500);
            exit();
        }
    }

    public function getRoomNameId(string $roomName): string|null
    {
        try {
            $query = "SELECT ID_R FROM rooms WHERE ROOM_NAME = :roomName";
            $params = [":roomName" => $roomName];

            $result = $this->utils->executeQuery($query, $params, true);

            return $result[0]["ID_R"] ?? null;
        } catch (Exception $e) {
            Utils::debug_log(
                [
                    "modelsErrorRoom-getRoomNameId" => $e->getMessage(),
                ],
                "error"
            );
            Utils::jsonResponse(["error" => "Internal server error"], 500);
            exit();
        }
    }

    public function getRoomById(string $roomId): array|null
    {
        try {
            $query = "SELECT * FROM rooms WHERE ID_R = :roomId";
            $params = [":roomId" => $roomId];

            $result = $this->utils->executeQuery($query, $params, true);

            return $result[0] ?? null;
        } catch (Exception $e) {
            Utils::debug_log(
                [
                    "modelsErrorRoom-getRoomById" => $e->getMessage(),
                ],
                "error"
            );
            Utils::jsonResponse(["error" => "Internal server error"], 500);
            exit();
        }
    }

    public function doesRoomNameExist(string $roomName): bool
    {
        try {
            $query = "SELECT COUNT(*) FROM rooms WHERE ROOM_NAME = :roomName";
            $params = [":roomName" => $roomName];

            $result = $this->utils->executeQuery($query, $params, true);

            return $result[0]["COUNT(*)"] > 0;
        } catch (Exception $e) {
            Utils::debug_log(
                [
                    "modelsErrorRoom-doesRoomNameExist" => $e->getMessage(),
                ],
                "error"
            );
            Utils::jsonResponse(["error" => "Internal server error"], 500);
            exit();
        }
    }

    public function getRooms(): array|null
    {
        try {
            $query = "SELECT * FROM rooms";

            $result = $this->utils->executeQuery($query, [], true);

            return $result ?? null;
        } catch (Exception $e) {
            Utils::debug_log(
                [
                    "modelsErrorRoom-getRooms" => $e->getMessage(),
                ],
                "error"
            );
            Utils::jsonResponse(["error" => "Internal server error"], 500);
            exit();
        }
    }
}
