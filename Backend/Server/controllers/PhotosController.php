<?php

namespace controllers;

require_once __DIR__ . '/../vendor/autoload.php'; // Carrega o autoload das dependências (composer)

use models\PhotoModel;  // Importa o modelo PhotoModel para interagir com fotos
use tools\Utils;        // Importa a classe Utils para funções auxiliares, como resposta JSON
use Exception;          // Importa a classe Exception para captura de erros
use core\JwtHandler;    // (não utilizado neste código, mas provavelmente será utilizado em outras partes)

class PhotosController
{
    // Instância do modelo de fotos
    private $photoModel;

    // Construtor da classe, inicializa o modelo de fotos
    public function __construct()
    {
        $this->photoModel = new PhotoModel(); // Cria uma nova instância de PhotoModel
    }

    // Método que tira uma foto com base em um assunto específico
    public function takePhotoWhithByMatter()
    {
        // Chama o método do modelo para tirar uma foto, passando o 'matter' (assunto) do POST
        $photo = $this->photoModel->takePhotoWhithByMatter($_POST['matter']);

        // Retorna uma resposta JSON com o resultado, status 201 (Criado)
        Utils::jsonResponse(['message' => $photo], 201);
    }
}
