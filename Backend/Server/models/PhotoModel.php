<?php

namespace models;

use tools\Utils;
use Exception;

class PhotoModel
{
    private Utils $utils;

    /**
     * Constructor of the class. Instantiates the Utils object to perform database operations.
     */
    public function __construct()
    {
        $this->utils = new Utils();
    }

    /**
     * Gets the photos related to a specific subject.
     *
     * @param string $subject The subject that will be used as a filter for the photos
     * @return ?string List of photos related to the subject
     * @throws Exception If an error occurs when executing the database query
     */
    public function takePhotoWithMatter(string $subject): ?string
    {
        try {
            $photoQuery = "SELECT * FROM photos WHERE MATTER = :subject";

            $queryParameters = [":subject" => $subject];

            $photoResult = $this->utils->executeQuery($photoQuery, $queryParameters, true);

            return $photoResult[0]["ADDRESS"] ?? null;
        } catch (Exception $e) {
            Utils::debug_log(
                [
                    "modelsErrorPhoto-takePhotoWithMatter" => $e->getMessage(),
                ],
                "error"
            );
            Utils::jsonResponse(["error" => "Internal server error"], 500);

            return null;
        }
    }
}