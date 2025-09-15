<?php

namespace App\Trait;

use App\Enums\HttpStatus;

trait ApiResponse
{
    protected function successResponse($data = [], string $message = 'Success', int $code = HttpStatus::OK->value)
    {
        return response()->json([
            'status'  => true,
            'message' => $message,
            'data'    => $data,
        ], $code);
    }

    protected function errorResponse(string $message = 'Error', int $code = HttpStatus::INTERNET_SERVER_ERROR->value, $errors = null)
    {
        $response = [
            'status'  => false,
            'message' => $message,
        ];

        if ($errors) {
            $response['errors'] = $errors;
        }

        return response()->json($response, $code);
    }
}
