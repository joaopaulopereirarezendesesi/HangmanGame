<?php

namespace models;

use tools\Utils;
use Exception;

class PhotoModel
{
    private Utils $utils;

    /**
     * Construtor da classe. Instancia o objeto Utils para realizar operações de banco de dados.
     */
    public function __construct()
    {
        $this->utils = new Utils();
    }

    /**
     * Obtém as fotos relacionadas a uma matéria específica.
     *
     * @param string $matter A matéria que será utilizada como filtro para as fotos
     * @return array Lista de fotos relacionadas à matéria
     * @throws Exception Se ocorrer um erro ao executar a consulta no banco de dados
     */
    public function takePhotoWithMatter(string $matter): array
    {
        try {
            $query = "SELECT * FROM photos WHERE MATTER = :matter";

            $params = [":matter" => $matter];

            $result = $this->utils->executeQuery($query, $params, true);

            return $result[0]["ADDRESS"] ?? [];
        } catch (Exception $e) {
            Utils::debug_log(
                [
                    "modelsErrorPhoto-takePhotoWithMatter" => $e->getMessage(),
                ],
                "error"
            );
            Utils::jsonResponse(["error" => "Internal server error"], 500);
            exit();
        }
    }
}
