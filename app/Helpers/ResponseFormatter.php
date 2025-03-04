<?php

namespace App\Helpers;

use Illuminate\Http\JsonResponse;

/**
 * Format response.
 */
class ResponseFormatter
{
    /**
     * Default API Response Structure
     *
     * @var array
     */
    protected static $response = [
        'meta' => [
            'code' => 200,
            'status' => 'success',
            'message' => null,
        ],
        'result' => null,
    ];

    /**
     * Send a success response.
     *
     * @param mixed  $data    The response data
     * @param string $message The success message
     * @param int    $code    The HTTP status code (default: 200)
     * 
     * @return JsonResponse
     */
    public static function success($data = null, $message = 'Success', $code = 200): JsonResponse
    {
        self::$response['meta'] = [
            'code' => $code,
            'status' => 'success',
            'message' => $message,
        ];
        self::$response['result'] = $data;

        return response()->json(self::$response, $code);
    }

    /**
     * Send an error response.
     *
     * @param string $message The error message
     * @param int    $code    The HTTP status code (default: 400)
     * @param mixed  $errors  Additional error details (optional)
     * 
     * @return JsonResponse
     */
    public static function error($message = 'Error', $code = 400, $errors = null): JsonResponse
    {
        self::$response['meta'] = [
            'code' => (int) $code, // Pastikan kode selalu integer
            'status' => 'error',
            'message' => $message,
        ];
        self::$response['result'] = $errors; // Bisa berisi detail error atau null

        return response()->json(self::$response, $code);
    }
}
