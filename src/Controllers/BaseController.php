<?php

namespace Controllers;

class BaseController
{
    protected function jsonResponse($data, $statusCode = 200)
    {
        http_response_code($statusCode);
        return json_encode($data);
    }

    protected function errorResponse($message, $statusCode = 400)
    {
        return $this->jsonResponse(['error' => $message], $statusCode);
    }

    protected function getRequestBody()
    {
        return json_decode(file_get_contents('php://input'), true);
    }
}