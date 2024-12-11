<?php

class RoomModel
{
    private $db;

    public function __construct()
    {
        $this->db = Database::connect();
    }

    public function getCurrentRoomTime()
    {
        $dateTime = new DateTime('now', new DateTimeZone('UTC'));
        return $dateTime->format('Y-m-d H:i:s.u');
    }

    public function createRoom($id_o, $room_name, $private, $password, $player_capacity, $time_limit, $points)
    {
        try {
            $query = "INSERT INTO rooms (ID_O, ROOM_NAME, PRIVATE, PASSWORD, PLAYER_CAPACITY, TIME_LIMIT) 
                      VALUES (:id_o, :room_name, :private, :password, :player_capacity, :time_limit, :points)";

            $stmt = $this->db->prepare($query);
            $stmt->execute([
                ':id_o' => $id_o,
                ':room_name' => $room_name,
                ':private' => $private,
                ':password' => password_hash($password, PASSWORD_ARGON2ID),
                ':player_capacity' => $player_capacity,
                ':time_limit' => $time_limit,
                ':points' => $points
            ]);

            return json_encode(['idroom' => $this->getRoomNameId($room_name)]);
        } catch (PDOException $e) {
            throw new Exception("Erro ao criar sala: " . $e->getMessage());
        }
    }

    public function getRoomNameId($roomName)
    {
        try {
            $query = "SELECT ID_R FROM rooms WHERE ROOM_NAME = :roomName";
            $stmt = $this->db->prepare($query);
            $stmt->bindParam(':roomId', $roomName, PDO::PARAM_INT);
            $stmt->execute();

            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            throw new Exception("Erro ao obter o ID da sala: " . $e->getMessage());
        }
    }

    public function getRoomById($roomId)
    {
        try {
            $query = "SELECT * FROM rooms WHERE ID_R = :roomId";
            $stmt = $this->db->prepare($query);
            $stmt->bindParam(':roomId', $roomId, PDO::PARAM_INT);
            $stmt->execute();

            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            throw new Exception("Erro ao obter sala: " . $e->getMessage());
        }
    }

    public function doesRoomNameExist($roomName)
    {
        try {
            $query = "SELECT COUNT(*) FROM rooms WHERE ROOM_NAME = :roomName";
            $stmt = $this->db->prepare($query);
            $stmt->bindParam(':roomName', $roomName, PDO::PARAM_STR);
            $stmt->execute();

            return $stmt->fetchColumn() > 0;
        } catch (PDOException $e) {
            throw new Exception("Erro ao verificar nome da sala: " . $e->getMessage());
        }
    }

    public function getIdroomByName($roomName)
    {
        try {
            $query = "SELECT ID_R FROM rooms WHERE ROOM_NAME = :roomName";
            $stmt = $this->db->prepare($query);
            $stmt->bindParam(':roomName', $roomName, PDO::PARAM_STR);
            $stmt->execute();

            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            return $result['ID_R'] ?? null;
        } catch (PDOException $e) {
            throw new Exception("Erro ao obter ID da sala: " . $e->getMessage());
        }
    }
}
