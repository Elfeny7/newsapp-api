<?php

namespace App\Support;

use Illuminate\Support\Facades\Log;

class ApiResponse
{
    public static function error($e, $message = "Internal server error", $code = 500)
    {
        Log::error($e->getMessage(), ['trace' => $e->getTraceAsString()]);
        return response()->json([
            'success' => false,
            'message' => $message,
        ], $code);
    }

    public static function validationError($errors, $message = "Validation error", $code = 422)
    {
        return response()->json([
            'success' => false,
            'message' => $message,
            'errors' => $errors
        ], $code);
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