<?php

namespace App\Controllers;

use App\Services\ResponseService;
use Throwable;

class BaseController
{
    // ensures all expected fields are set in data object and sends a bad request response if not
    // used to make sure all expected $_POST fields are at least set, additional validation may still need to be set
    function validateInput($expectedFields, $data): void
    {
        foreach ($expectedFields as $field) {
            if (!isset($data[$field])) {
                ResponseService::Send("Required field: $field, is missing", 400);
                error_log("Required field: $field, is missing");
                exit();
            }
        }
    }

    // gets the post data and returns it as an array
    function decodePostData()
    {
        try {
            return json_decode(file_get_contents('php://input'), true);
        } catch (Throwable $th) {
            ResponseService::Error("error decoding JSON in request body", 400);
            error_log($th->getMessage());
        }
    }
}