<?php

namespace controllers;

require_once __DIR__ . "/../vendor/autoload.php";

use models\PhotoModel;
use tools\Utils;
use Exception;

/**
 * Class responsible for managing photo-related operations.
 */
class PhotosController
{
    private PhotoModel $photoModel;

    /**
     * Initializes the photo model instance.
     */
    public function __construct()
    {
        $this->photoModel = new PhotoModel();
    }

    /**
     * Captures a photo based on a specific subject.
     *
     * @param string $matter The subject of the photo.
     * @return ?string The path of the photo or null in case of failure.
     */
    public function takePhotoWithMatter(string $matter): ?string
    {
        try {
            return $this->photoModel->takePhotoWithMatter($matter);
        } catch (Exception $e) {
            Utils::debug_log(
                [
                    "controllerErrorPhotos-takePhotoWithMatter" => $e->getMessage(),
                ],
                "error"
            );
            Utils::jsonResponse(["error" => "Internal server error"], 500);

            return null;
        }
    }
}
