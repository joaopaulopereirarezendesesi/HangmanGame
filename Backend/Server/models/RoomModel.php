<?php

namespace models;

use tools\Utils;
use Exception;
use DateTime;
use DateTimeZone;

class RoomModel
{
    private $utils;

    public function __construct()
    {
        $this->utils = new Utils();
    }

    public function getCurrentRoomTime()
    {
        $dateTime = new DateTime('now', new DateTimeZone('UTC'));
        return $dateTime->format('Y-m-d H:i:s.u');
    }

    public function createRoom($id_o, $room_name, $private, $password, $player_capacity, $time_limit, $points)
    {
        try {
            $query = "INSERT INTO rooms (ID_R, ID_O, ROOM_NAME, PRIVATE, PASSWORD, PLAYER_CAPACITY, TIME_LIMIT, POINTS) 
                    VALUES (UUID(), :id_o, :room_name, :private, :password, :player_capacity, :time_limit, :points)";

            $params = [
                ':id_o' => $id_o,
                ':room_name' => $room_name,
                ':private' => $private,
                ':password' => $private ? password_hash($password, PASSWORD_ARGON2ID) : null,
                ':player_capacity' => $player_capacity,
                ':time_limit' => $time_limit,
                ':points' => $points
            ];

            $this->utils->executeQuery($query, $params);

            return $this->getRoomNameId($room_name);
        } catch (Exception $e) {
            throw new Exception("Erro ao criar sala: " . $e->getMessage());
        }
    }

    public function getRoomNameId($roomName)
    {
        try {
            $query = "SELECT ID_R FROM rooms WHERE ROOM_NAME = :roomName";
            $params = [':roomName' => $roomName];

            $result = $this->utils->executeQuery($query, $params, true);

            return $result[0]['ID_R'] ?? null;
        } catch (Exception $e) {
            throw new Exception("Erro ao obter o ID da sala: " . $e->getMessage());
        }
    }

    public function getRoomById($roomId)
    {
        try {
            $query = "SELECT * FROM rooms WHERE ID_R = :roomId";
            $params = [':roomId' => $roomId];

            $result = $this->utils->executeQuery($query, $params, true);

            return $result[0] ?? null;
        } catch (Exception $e) {
            throw new Exception("Erro ao obter sala: " . $e->getMessage());
        }
    }

    public function doesRoomNameExist($roomName)
    {
        try {
            $query = "SELECT COUNT(*) FROM rooms WHERE ROOM_NAME = :roomName";
            $params = [':roomName' => $roomName];

            $result = $this->utils->executeQuery($query, $params, true);

            return $result[0]['COUNT(*)'] > 0;
        } catch (Exception $e) {
            throw new Exception("Erro ao verificar nome da sala: " . $e->getMessage());
        }
    }

    public function getRooms()
    {
        try {
            $query = "SELECT * FROM rooms";

            $result = $this->utils->executeQuery($query, [], true);

            return $result ?? null;
        } catch (Exception $e) {
            throw new Exception("Erro ao obter sala: " . $e->getMessage());
        }
    }
}
