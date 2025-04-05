<?php

namespace controllers;

require_once __DIR__ . "/../vendor/autoload.php";

use models\RoomModel;
use models\PlayedModel;
use controllers\PhotosController;
use tools\Utils;
use Exception;

class RoomController
{
    private RoomModel $roomModel;
    private PlayedModel $playedModel;
    private PhotosController $photosController;

    /**
     * RoomController class constructor.
     * Initializes the necessary models and the photo controller.
     */
    public function __construct()
    {
        $this->photosController = new PhotosController();
        $this->roomModel = new RoomModel();
        $this->playedModel = new PlayedModel();
    }

    /**
     * Creates a new room.
     *
     * This method receives data via POST, validates the provided information
     * and creates a new room in the system.
     *
     * @return void Returns a JSON response with the details of the created room.
     */
    public function createRoom(): void
    {
        try {
            $userId = Utils::getUserIdFromToken();
            if (!$userId) {
                Utils::jsonResponse(["error" => "Token not provided"], 403);
            }

            $data = $_POST;

            if (!is_array($data)) {
                Utils::jsonResponse(["error" => "Invalid data."], 404);
            }

            $modality = strtolower($data["modality"]);
            $modalityImage = strval(
                $this->photosController->takePhotoWithMatter($modality)
            );

            $points = isset($data["points"]) ? intval($data["points"]) : 2000;
            $roomName = strval($data["room_name"]) ?? "room_" . uniqid();
            $isPrivate = filter_var(
                $data["private"] ?? false,
                FILTER_VALIDATE_BOOLEAN
            );

            if ($this->roomModel->doesRoomNameExist($roomName)) {
                Utils::jsonResponse(
                    ["error" => "Room name already in use."],
                    400
                );
            }

            $password = strval($data["password"]) ?? "";
            if ($isPrivate && empty($password)) {
                Utils::jsonResponse(
                    ["error" => "Password required for private rooms."],
                    400
                );
            }

            $playerCapacity = isset($data["player_capacity"])
                ? intval($data["player_capacity"])
                : 10;

             if ($playerCapacity >= 20 || $playerCapacity <= 2) {
                   Utils::jsonResponse(
                    ["error" => "Invalid player capacity."],
                    400
                 );
             }

            $timeLimit = isset($data["time_limit"])
                ? intval($data["time_limit"])
                : 5;

            if ($playerCapacity < 2 || $timeLimit < 2) {
                Utils::jsonResponse(
                    ["error" => "Invalid capacity or time limit."],
                    400
                );
            }

            $roomId = $this->roomModel->createRoom(
                strval($userId),
                $roomName,
                $isPrivate,
                $password,
                $playerCapacity,
                $timeLimit,
                $points,
                $modality,
                $modalityImage
            );

            Utils::jsonResponse([
                "id_creator" => $userId,
                "id_room" => $roomId,
                "room_name" => $roomName,
                "private" => $isPrivate,
                "capacity" => $playerCapacity,
                "timeout" => $timeLimit,
                "points" => $points,
                "modality" => $modality,
            ]);
        } catch (Exception $e) {
            Utils::debug_log(
                [
                    "controllerErrorRoom-createRoom" => $e->getMessage(),
                ],
                "error"
            );
            Utils::jsonResponse(["error" => "Internal server error"], 500);
        }
    }

    /**
     * Allows a user to join an existing room.
     *
     * This method validates if the room exists, if it is full, and if the password is correct
     * for private rooms. Otherwise, the user is added to the room.
     *
     * @return void Returns a JSON response with the operation status.
     */
    public function joinRoom(): void
    {
        try {
            $userId = Utils::getUserIdFromToken();
            if (!$userId) {
                Utils::jsonResponse(["error" => "Token not provided"], 403);
            }

            $data = $_POST;
            $roomId = $data["roomId"];
            $room = $this->roomModel->getRoomById($roomId);

            if (!$room) {
                Utils::jsonResponse(["error" => "Room not found."], 404);
            }

            if (
                $room["PRIVATE"] &&
                !Utils::validateRoomPassword(
                    $room["PASSWORD"],
                    $data["password"]
                )
            ) {
                Utils::jsonResponse(["error" => "Invalid password."], 400);
            }

            if (
                $this->playedModel->getPlayersCountInRoom($roomId) >=
                $room["PLAYER_CAPACITY"]
            ) {
                Utils::jsonResponse(["error" => "Room is full."], 403);
            }

            $this->playedModel->joinRoom(
                strval($userId),
                strval($roomId)
            );
            Utils::jsonResponse(["message" => "Successfully joined the room."]);
        } catch (Exception $e) {
            Utils::debug_log(
                [
                    "controllerErrorRoom-joinRoom" => $e->getMessage(),
                ],
                "error"
            );
            Utils::jsonResponse(["error" => "Internal server error"], 500);
        }
    }

    /**
     * Removes a player from a room.
     *
     * This method allows a user with permission to remove a player from the room.
     *
     * @return void Returns a JSON response indicating the operation status.
     */
    public function removePlayerFromRoom(): void
    {
        try {
            $userId = Utils::getUserIdFromToken();
            if (!$userId) {
                Utils::jsonResponse(["error" => "Token not provided"], 403);
            }

            $data = $_POST;
            $roomId = $data["roomId"];

            if (!$this->roomModel->getRoomById($roomId)) {
                Utils::jsonResponse(["error" => "Room not found."], 404);
            }

            $this->playedModel->leaveRoom(
                strval($userId),
                strval($roomId)
            );
            Utils::jsonResponse(["message" => "Player successfully removed."]);
        } catch (Exception $e) {
            Utils::debug_log(
                [
                    "controllerErrorRoom-removePlayerFromRoom" => $e->getMessage(),
                ],
                "error"
            );
            Utils::jsonResponse(["error" => "Internal server error"], 500);
        }
    }

    /**
     * Gets the list of available rooms.
     *
     * This method returns all available rooms in the system.
     *
     * @return void Returns a JSON response with the list of rooms or an error message.
     */
    public function getRooms(): void
    {
        try {
            $userId = Utils::getUserIdFromToken();
            if (!$userId) {
                Utils::jsonResponse(["error" => "Token not provided"], 403);
            }

            $rooms = $this->roomModel->getRooms();
            if ($rooms) {
                Utils::jsonResponse(["rooms" => $rooms]);
            } else {
                Utils::jsonResponse(["message" => "No rooms found"], 404);
            }
        } catch (Exception $e) {
            Utils::debug_log(
                [
                    "controllerErrorRoom-getRooms" => $e->getMessage(),
                ],
                "error"
            );
            Utils::jsonResponse(["error" => "Internal server error"], 500);
        }
    }

    /**
     * Counts the number of players in a room.
     *
     * This method returns the current number of players in a specific room.
     *
     * @return void Returns a JSON response with the player count.
     */
    public function countPlayers(): void
    {
        try {
            $userId = Utils::getUserIdFromToken();
            if (!$userId) {
                Utils::jsonResponse(["error" => "Token not provided"], 403);
            }

            $data = $_POST;
            $roomId = $data["roomId"];

            if (!isset($roomId)) {
                Utils::jsonResponse(["error" => "Room ID not provided"], 400);
            }

            $playerCount = $this->playedModel->countPlayersInRoom(
                strval($roomId)
            );
            Utils::jsonResponse(["players" => $playerCount]);
        } catch (Exception $e) {
            Utils::debug_log(
                [
                    "controllerErrorRoom-countPlayers" => $e->getMessage(),
                ],
                "error"
            );
            Utils::jsonResponse(["error" => "Internal server error"], 500);
        }
    }
}