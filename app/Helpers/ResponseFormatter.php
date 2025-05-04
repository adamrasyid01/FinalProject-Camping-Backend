<?php

namespace App\Helpers;

/**
 * Format response.
 */
class ResponseFormatter
{
    /**
     * API Response
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
     * Give success response.
     */
    public static function success($data = null, $message = null)
    {
        self::$response['meta']['message'] = $message;

        // Deteksi jika data adalah paginasi
        if ($data instanceof \Illuminate\Pagination\LengthAwarePaginator) {
            self::$response['result'] = $data->items(); // hanya ambil item data-nya
            self::$response['meta']['pagination'] = [
                'current_page' => $data->currentPage(),
                'last_page' => $data->lastPage(),
                'limit' => $data->perPage(),
                'total' => $data->total(),
            ];
        } else {
            self::$response['result'] = $data;
        }

        return response()->json(self::$response, self::$response['meta']['code']);
    }


    /**
     * Give error response.
     */
    public static function error($message = null, $code = 400)
    {
        self::$response['meta']['status'] = 'error';
        self::$response['meta']['code'] = $code;
        self::$response['meta']['message'] = $message;

        return response()->json(self::$response, self::$response['meta']['code']);
    }
}
