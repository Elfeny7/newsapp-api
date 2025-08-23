<?php

namespace App\Support;

use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Support\Facades\Log;

class ApiResponse
{
    public static function throw($e, $message = "Internal server error", $code = 500)
    {
        Log::error($e->getMessage(), ['trace' => $e->getTraceAsString()]);
        $response = [
            'success' => false,
            'message' => $message,
        ];
        throw new HttpResponseException(response()->json($response, $code));
    }

    public static function validationError($errors, $message = "Validation error", $code = 422)
    {
        $response = [
            'success' => false,
            'message' => $message,
            'errors' => $errors
        ];
        throw new HttpResponseException(response()->json($response, $code));
    }

    public static function success($result = null, $message = "Success", $code = 200)
    {
        $response = [
            'success' => true,
            'message' => $message
        ];

        if (!empty($result)) {
            $response['data'] = $result;
        }

        return response()->json($response, $code);
    }
}