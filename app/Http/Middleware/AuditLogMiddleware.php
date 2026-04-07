<?php

namespace App\Http\Middleware;

use App\Models\AuditLog;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class AuditLogMiddleware
{
    private const AUDITABLE_METHODS = ['POST', 'PUT', 'PATCH', 'DELETE'];
    private const SENSITIVE_FIELDS = ['password', 'password_confirmation', 'token', 'secret', 'iban', 'bic'];

    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        if (!in_array($request->method(), self::AUDITABLE_METHODS)) {
            return $response;
        }

        try {
            $inputData = $this->sanitizeData($request->all());
            $action = $this->determineAction($request);

            $logData = [
                'user_id' => $request->user()?->id,
                'action' => $action,
                'auditable_type' => null,
                'auditable_id' => null,
                'old_values' => null,
                'new_values' => $inputData ?: null,
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent(),
                'created_at' => now(),
            ];

            // Log failed mutation requests for security monitoring
            if (!$response->isSuccessful()) {
                $logData['action'] = '[FAILED:' . $response->getStatusCode() . '] ' . $action;
            }

            AuditLog::create($logData);
        } catch (\Exception $e) {
            // Don't let audit logging break the main flow
            report($e);
        }

        return $response;
    }

    private function sanitizeData(array $data): array
    {
        foreach (self::SENSITIVE_FIELDS as $field) {
            if (isset($data[$field])) {
                $data[$field] = '***REDACTED***';
            }
        }
        return $data;
    }

    private function determineAction(Request $request): string
    {
        $method = $request->method();
        $path = $request->path();

        return "{$method} /{$path}";
    }
}
