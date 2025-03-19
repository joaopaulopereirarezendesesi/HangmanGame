<?php

namespace controllers;

require_once __DIR__ . '/../vendor/autoload.php';

use models\PhotosModel;
use tools\Utils;
use core\JwtHandler;

class PhotosController
{
    private $photoModel;

    public function __construct()
    {
        $this->photoModel = new PhotosModel();
    }

    public function takePhotoWhithByMatter() {
        $photo = $this->photoModel->takePhotoWhithByMatter($_POST['matter']);
        Utils::jsonResponse(['message' => $photo], 201);
    }
}
