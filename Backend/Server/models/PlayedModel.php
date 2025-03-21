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
        $this->utils = new Utils();  // Instancia a classe Utils para poder executar funções como consultas no banco de dados.
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
            // Consulta SQL que conta o número de jogadores na sala especificada pelo roomId
            $query = "SELECT COUNT(*) AS player_count FROM played WHERE ID_R = :roomId";
            $params = [':roomId' => $roomId];  // Parâmetro para a consulta (ID da sala)
            // Executa a consulta e retorna o número de jogadores
            $result = $this->utils->executeQuery($query, $params, true);

            // Retorna o número de jogadores ou 0 caso não haja resultados
            return $result[0]['player_count'] ?? 0;
        } catch (Exception $e) {
            // Lança uma exceção com a mensagem de erro caso algo dê errado
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
            // Consulta SQL que insere um novo registro na tabela 'played', indicando que o usuário entrou na sala
            $query = "INSERT INTO played (ID_PLAYED, ID_U, ID_R, SCORE, IS_THE_CHALLENGER) VALUES (UUID(), :userId, :roomId, 0, 0)";
            $params = [':userId' => $userId, ':roomId' => $roomId];  // Parâmetros da consulta (ID do usuário e da sala)
            // Executa a consulta para adicionar o jogador na sala
            $this->utils->executeQuery($query, $params);
        } catch (Exception $e) {
            // Lança uma exceção com a mensagem de erro caso algo dê errado
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
            // Consulta SQL que deleta o registro do jogador na sala
            $query = "DELETE FROM played WHERE ID_U = :userId AND ID_R = :roomId";
            $params = [':userId' => $userId, ':roomId' => $roomId];  // Parâmetros da consulta (ID do usuário e da sala)
            // Executa a consulta para remover o jogador da sala
            $this->utils->executeQuery($query, $params);
        } catch (Exception $e) {
            // Lança uma exceção com a mensagem de erro caso algo dê errado
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
            // Consulta SQL que conta o número de jogadores na sala especificada pelo roomId
            $query = "SELECT COUNT(*) as player_count FROM played WHERE ID_R = :roomId";
            $params = [':roomId' => $roomId];  // Parâmetro para a consulta (ID da sala)
            // Executa a consulta e retorna o número de jogadores
            $result = $this->utils->executeQuery($query, $params, true);

            // Retorna o número de jogadores ou 0 caso não haja resultados
            return $result[0]['player_count'] ?? 0;
        } catch (Exception $e) {
            // Retorna a mensagem de erro em caso de falha
            return "Erro: " . $e->getMessage();
        }
    }
}
