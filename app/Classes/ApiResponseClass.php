<?php

namespace App\Classes;

use Illuminate\Support\Facades\DB;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Support\Facades\Log;

class ApiResponseClass
{
     public static function rollback($e, $message = "Something went wrong! Process not completed")
     {
          if (DB::transactionLevel() > 0) {
               DB::rollBack();
          }
          self::throw($e, $message);
     }

     public static function throw($e, $message = "Something went wrong! Process not completed", $code = 500)
     {
          Log::error($e->getMessage(), ['trace' => $e->getTraceAsString()]);
          $response = [
               'success' => false,
               'message' => $message,
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
