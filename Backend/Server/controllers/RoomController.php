<?php

namespace controllers;

require_once __DIR__ . "/../vendor/autoload.php";

use models\RoomModel;
use models\PlayedModel;
use controllers\PhotosController;
use tools\Utils;
use DateTime;
use DateTimeZone;
use Exception;

class RoomController
{
    private RoomModel $roomModel;
    private PlayedModel $playedModel;
    private PhotosController $photosController;

    /**
     * Construtor da classe RoomController.
     * Inicializa os modelos necessários e o controlador de fotos.
     */
    public function __construct()
    {
        $this->photosController = new PhotosController();
        $this->roomModel = new RoomModel();
        $this->playedModel = new PlayedModel();
    }

    /**
     * Cria uma nova sala.
     *
     * Este método recebe dados via POST, valida as informações fornecidas
     * e cria uma nova sala no sistema.
     *
     * @return void Retorna uma resposta JSON com os detalhes da sala criada.
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
            $modalityImg = strval(
                $this->photosController->takePhotoWithMatter($modality)
            );

            $points = isset($data["points"]) ? intval($data["points"]) : 2000;
            $roomName = strval($data["room_name"]) ?? $this->generateRoomName();
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
                $modalityImg
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
     * Permite que um usuário entre em uma sala existente.
     *
     * Este método valida se a sala existe, se está cheia, e se a senha está correta
     * para salas privadas. Caso contrário, o usuário é adicionado à sala.
     *
     * @return void Retorna uma resposta JSON com o status da operação.
     */
    public function joinRoom(): void
    {
        try {
            $userId = Utils::getUserIdFromToken();
            if (!$userId) {
                Utils::jsonResponse(["error" => "Token not provided"], 403);
            }

            $data = $_POST;
            $room = $this->roomModel->getRoomById($data["roomId"]);

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
                $this->playedModel->getPlayersCountInRoom($data["roomId"]) >=
                $room["PLAYER_CAPACITY"]
            ) {
                Utils::jsonResponse(["error" => "Room is full."], 403);
            }

            $this->playedModel->joinRoom(
                strval($userId),
                strval($data["roomId"])
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

            if (!$this->roomModel->getRoomById($data["roomId"])) {
                Utils::jsonResponse(["error" => "Room not found."], 404);
            }

            $this->playedModel->leaveRoom(
                strval($userId),
                strval($data["roomId"])
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
     * Obtém a lista de salas disponíveis.
     *
     * Este método retorna todas as salas disponíveis no sistema.
     *
     * @return void Retorna uma resposta JSON com a lista de salas ou mensagem de erro.
     */
    public function getRooms(): void
    {
        try {
            $authUserId = Utils::getUserIdFromToken();
            if (!$authUserId) {
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
     * Conta o número de jogadores em uma sala.
     *
     * Este método retorna o número atual de jogadores em uma sala específica.
     *
     * @return void Retorna uma resposta JSON com a contagem de jogadores.
     */
    public function countPlayers(): void
    {
        try {
            $authUserId = Utils::getUserIdFromToken();
            if (!$authUserId) {
                Utils::jsonResponse(["error" => "Token not provided"], 403);
            }

            $data = $_POST;

            if (!isset($data["roomId"])) {
                Utils::jsonResponse(["error" => "Room ID not provided"], 400);
            }

            $playerCount = $this->playedModel->countPlayersInRoom(
                strval($data["roomId"])
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

    /**
     * Gera um nome único para uma sala com base no horário atual.
     *
     * @return string Retorna o nome gerado para a sala.
     */
    private function generateRoomName(): ?string
    {
        try {
            $dateTime = new DateTime('now', new DateTimeZone('UTC'));
            $formattedDate = $dateTime->format('Y-m-d H:i:s');

            return "room_" . $formattedDate;
        } catch (Exception $e) {
            Utils::debug_log(
                [
                    "controllerErrorRoom-generateRoomName" => $e->getMessage(),
                ],
                "error"
            );
            Utils::jsonResponse(["error" => "Internal server error"], 500);

            return null;
        }
    }
}
