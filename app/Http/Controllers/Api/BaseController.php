<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller as Controller;

class BaseController extends Controller
{
    /**
     * success response method.
     *
     * @param $message
     * @param $result
     * @return \Illuminate\Http\JsonResponse
     */
    public function sendResponse($message, $result)
    {
        $response = [
            'message' => $message,
            'data'    => $result,
        ];

        return response()->json($response, 200);
    }

    /**
     * return error response.
     *
     * @param $error
     * @param array $errorMessages
     * @param int $code
     * @return \Illuminate\Http\JsonResponse
     */
    public function sendError($error, $errorMessages = [], $code = 404)
    {
        $response = [
            'message' => $error,
        ];

        if (!empty($errorMessages)) {
            $response['data'] = $errorMessages;
        }

        return response()->json($response, $code);
    }
}
