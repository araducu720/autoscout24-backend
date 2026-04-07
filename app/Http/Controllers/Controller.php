<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Http\JsonResponse;

abstract class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    /**
     * Return a success JSON response.
     */
    protected function success($data = null, string $message = 'Success', int $statusCode = 200): JsonResponse
    {
        return response()->json([
            'success' => true,
            'message' => $message,
            'data' => $data,
            'timestamp' => now()->toISOString(),
        ], $statusCode);
    }

    /**
     * Return an error JSON response.
     */
    protected function error(string $message = 'An error occurred', int $statusCode = 500, array $details = []): JsonResponse
    {
        return response()->json([
            'success' => false,
            'message' => $message,
            'status' => $statusCode,
            'details' => $details,
            'timestamp' => now()->toISOString(),
        ], $statusCode);
    }

    /**
     * Return a validation error response.
     */
    protected function validationError(array $errors, string $message = 'Validation failed'): JsonResponse
    {
        return response()->json([
            'success' => false,
            'message' => $message,
            'errors' => $errors,
            'status' => 422,
            'timestamp' => now()->toISOString(),
        ], 422);
    }

    /**
     * Return a paginated response.
     */
    protected function paginated($data, string $message = 'Success'): JsonResponse
    {
        return response()->json([
            'success' => true,
            'message' => $message,
            'data' => $data->items(),
            'pagination' => [
                'total' => $data->total(),
                'count' => $data->count(),
                'per_page' => $data->perPage(),
                'current_page' => $data->currentPage(),
                'last_page' => $data->lastPage(),
            ],
            'timestamp' => now()->toISOString(),
        ], 200);
    }
}
