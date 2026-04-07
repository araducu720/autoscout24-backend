<?php

namespace App\Exceptions;

use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;

class ApiException extends Exception
{
    /**
     * The HTTP status code.
     */
    public int $statusCode;

    /**
     * The error details.
     */
    public array $details = [];

    /**
     * Create a new exception instance.
     */
    public function __construct(
        string $message = 'An error occurred',
        int $statusCode = 500,
        array $details = [],
        ?Exception $previous = null
    ) {
        parent::__construct($message, 0, $previous);
        $this->statusCode = $statusCode;
        $this->details = $details;
    }

    /**
     * Render the exception as a JSON response.
     */
    public function render(): JsonResponse
    {
        return response()->json([
            'success' => false,
            'message' => $this->message,
            'status' => $this->statusCode,
            'details' => $this->details,
            'timestamp' => now()->toISOString(),
        ], $this->statusCode);
    }

    /**
     * Report the exception.
     */
    public function report(): void
    {
        // Log critical errors
        if ($this->statusCode >= 500) {
            \Log::error('API Error: ' . $this->message, [
                'status' => $this->statusCode,
                'details' => $this->details,
                'trace' => $this->getTraceAsString(),
            ]);
        }
    }
}
