<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CompressResponse
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        // Only compress JSON responses
        if ($response->headers->get('Content-Type') === 'application/json') {
            // Check if client accepts gzip encoding
            $acceptEncoding = $request->header('Accept-Encoding', '');
            
            if (str_contains($acceptEncoding, 'gzip')) {
                $content = $response->getContent();
                
                if ($content && strlen($content) > 1024) { // Only compress if > 1KB
                    $compressed = gzencode($content, 6); // Compression level 6
                    
                    if ($compressed !== false) {
                        $response->setContent($compressed);
                        $response->headers->set('Content-Encoding', 'gzip');
                        $response->headers->set('Content-Length', strlen($compressed));
                    }
                }
            }
        }

        return $response;
    }
}
