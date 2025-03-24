<?php

namespace models;

use tools\Utils;
use Exception;

class PlayedModel
{
    private $utils;

    /**
     * Construtor da classe. Instancia o objeto Utils para realizar operações de banco de dados.
     */
    public function __construct()
    {
        $this->utils = new Utils();  
    }

    /**
     * Retorna o número de jogadores em uma sala.
     * 
     * @param int $roomId O ID da sala que será verificado
     * @return int O número de jogadores na sala
     * @throws Exception Se ocorrer um erro ao executar a consulta no banco de dados
     */
    public function getPlayersCountInRoom($roomId)
    {
        try {
            $query = "SELECT COUNT(*) AS player_count FROM played WHERE ID_R = :roomId";
            $params = [':roomId' => $roomId];  

            $result = $this->utils->executeQuery($query, $params, true);

            return $result[0]['player_count'] ?? 0;
        } catch (Exception $e) {
            throw new Exception("Erro ao contar jogadores na sala: " . $e->getMessage());
        }
    }

    /**
     * Adiciona um jogador em uma sala.
     * 
     * @param int $userId O ID do usuário a ser adicionado
     * @param int $roomId O ID da sala em que o usuário será adicionado
     * @throws Exception Se ocorrer um erro ao executar a consulta no banco de dados
     */
    public function joinRoom($userId, $roomId)
    {
        try {
            $query = "INSERT INTO played (ID_PLAYED, ID_U, ID_R, SCORE, IS_THE_CHALLENGER) VALUES (UUID(), :userId, :roomId, 0, 0)";
            $params = [':userId' => $userId, ':roomId' => $roomId];  

            $this->utils->executeQuery($query, $params);
        } catch (Exception $e) {
            throw new Exception("Erro ao entrar na sala: " . $e->getMessage());
        }
    }

    /**
     * Remove um jogador de uma sala.
     * 
     * @param int $userId O ID do usuário a ser removido
     * @param int $roomId O ID da sala em que o jogador será removido
     * @throws Exception Se ocorrer um erro ao executar a consulta no banco de dados
     */
    public function leaveRoom($userId, $roomId)
    {
        try {
            $query = "DELETE FROM played WHERE ID_U = :userId AND ID_R = :roomId";
            $params = [':userId' => $userId, ':roomId' => $roomId];  

            $this->utils->executeQuery($query, $params);
        } catch (Exception $e) {
            throw new Exception("Erro ao sair da sala: " . $e->getMessage());
        }
    }

    /**
     * Conta o número de jogadores em uma sala (função adicional com nome semelhante a getPlayersCountInRoom).
     * 
     * @param int $roomId O ID da sala que será verificado (valor padrão é 1)
     * @return int O número de jogadores na sala
     */
    public function countPlayersInRoom($roomId = 1)
    {
        try {
            $query = "SELECT COUNT(*) as player_count FROM played WHERE ID_R = :roomId";
            $params = [':roomId' => $roomId];  

            $result = $this->utils->executeQuery($query, $params, true);

            return $result[0]['player_count'] ?? 0;
        } catch (Exception $e) {
            return "Erro: " . $e->getMessage();
        }
    }
}
