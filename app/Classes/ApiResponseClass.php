<?php

namespace App\Classes;

use Illuminate\Support\Facades\DB;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Support\Facades\Log;

class ApiResponseClass
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
               'data' => $errors
          ];
          throw new HttpResponseException(response()->json($response, $code));
     }

     public static function sendResponse($result, $message, $code = 200)
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
