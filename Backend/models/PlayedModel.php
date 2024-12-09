<?php

class PlayedModel
{
    private $db;

    public function __construct()
    {
        $this->db = Database::connect();
    }

    public function getPlayersCountInRoom($roomId)
    {
        $query = "SELECT COUNT(*) AS player_count FROM played WHERE ID_R = ?";
        $stmt = $this->db->prepare($query);
        $stmt->bind_param('i', $roomId);
        $stmt->execute();
        $result = $stmt->get_result();
        $row = $result->fetch_assoc();

        return $row['player_count'];
    }

    public function joinRoom($userId, $roomId)
    {
        $query = "INSERT INTO played (ID_U, ID_R, SCORE, IS_THE_CHALLENGER) VALUES (?, ?, 0, 0)";
        $stmt = $this->db->prepare($query);
        $stmt->bind_param('ii', $userId, $roomId);
        $stmt->execute();
    }
}
