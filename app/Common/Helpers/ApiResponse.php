<?php

declare(strict_types=1);

namespace App\Common\Helpers;

use Illuminate\Http\JsonResponse;

final class ApiResponse
{
    public static function success(
        mixed $data = null,
        string $message = 'Success',
        int $status = 200,
        array $meta = [],
    ): JsonResponse {
        return response()->json([
            'success' => true,
            'status_code' => $status,
            'message' => $message,
            'data' => $data,
            'meta' => empty($meta) ? (object) [] : $meta,
            'error' => null,
        ], $status);
    }

    public static function error(
        string $message = 'Error',
        int $status = 400,
        string $errorCode = 'BAD_REQUEST',
        mixed $details = null,
    ): JsonResponse {
        return response()->json([
            'success' => false,
            'status_code' => $status,
            'message' => $message,
            'data' => null,
            'meta' => (object) [],
            'error' => [
                'code' => $errorCode,
                'details' => $details,
            ],
        ], $status);
    }
}
