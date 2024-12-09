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
        $dateTime = new DateTime();
        $dateTime->setTimezone(new DateTimeZone('UTC'));
        $formattedDate = $dateTime->format('Y-m-d H:i:s.') . sprintf("%03d", $dateTime->format('v'));

        return $formattedDate;
    }

    public function createRoom($id_o, $room_name, $private, $password, $player_capacity, $time_limit)
    {
        $query = "INSERT INTO rooms (ID_O, ROOM_NAME, PRIVATE, PASSWORD, PLAYER_CAPACITY, TIME_LIMIT) 
                  VALUES (:id_o, :room_name, :private, :password, :player_capacity, :time_limit)";

        $stmt = $this->db->prepare($query);

        $stmt->bindValue(':id_o', $id_o, PDO::PARAM_INT);
        $stmt->bindValue(':room_name', $room_name, PDO::PARAM_STR);
        $stmt->bindValue(':private', $private, PDO::PARAM_BOOL);
        $stmt->bindValue(':password', $password, PDO::PARAM_STR);
        $stmt->bindValue(':player_capacity', $player_capacity, PDO::PARAM_INT);
        $stmt->bindValue(':time_limit', $time_limit, PDO::PARAM_INT);

        if ($stmt->execute()) {
            return [
                'success' => 'Sala criada com sucesso',
                'id_r' => $this->getIdroomByName($room_name),
                'room_name' => $room_name,
                'id_o' => $id_o,
                'private' => $private,
                'password' => $password,
                'player_capacity' => $player_capacity,
                'time_limit' => $time_limit
            ];
        } else {
            return ['error' => 'Falha ao criar a sala'];
        }
    }

    public function getRoomById($roomId)
    {
        $query = "SELECT * FROM rooms WHERE ID_R = ?";
        $stmt = $this->db->prepare($query);
        $stmt->bindValue(1, $roomId, PDO::PARAM_INT);
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);

        return $result;
    }

    public function doesRoomNameExist($roomName)
    {
        $query = "SELECT COUNT(*) FROM rooms WHERE ROOM_NAME = ?";
        $stmt = $this->db->prepare($query);
        $stmt->bindValue(1, $roomName, PDO::PARAM_STR);
        $stmt->execute();
        $count = $stmt->fetchColumn();

        return $count > 0;
    }

    public function getIdroomByName($roomName)
    {
        $query = "SELECT ID_R FROM rooms WHERE ROOM_NAME = ?";
        $stmt = $this->db->prepare($query);
        $stmt->bindValue(1, $roomName, PDO::PARAM_STR);
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);

        return $result['ID_R'] ?? null;
    }
}
