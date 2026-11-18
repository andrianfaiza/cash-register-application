<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class SecurityHeaders
{
    /**
     * Menambahkan header keamanan umum pada setiap respons.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        // HSTS hanya dikirim di atas HTTPS.
        if ($request->secure()) {
            $response->headers->set(
                'Strict-Transport-Security',
                'max-age=31536000; includeSubDomains; preload'
            );
        }

        $response->headers->set('X-Frame-Options', 'SAMEORIGIN');
        $response->headers->set('X-Content-Type-Options', 'nosniff');
        $response->headers->set('Referrer-Policy', 'strict-origin-when-cross-origin');
        $response->headers->set('Permissions-Policy', 'geolocation=(), microphone=(), camera=(), payment=()');
        $response->headers->set('Cross-Origin-Opener-Policy', 'same-origin');
        $response->headers->set('Cross-Origin-Resource-Policy', 'same-origin');

        // CSP: longgar di script/style karena butuh inline (Alpine/Livewire) + CDN.
        $response->headers->set('Content-Security-Policy', implode(' ', [
            "default-src 'self'",
            "script-src 'self' cdn.jsdelivr.net npmcdn.com 'unsafe-inline' 'unsafe-eval'",
            "style-src 'self' cdn.jsdelivr.net 'unsafe-inline'",
            "img-src 'self' data:",
            "connect-src 'self'",
            "font-src 'self'",
            "base-uri 'self'",
            "frame-ancestors 'none'",
        ]));

        // Sembunyikan versi PHP di header.
        header_remove('X-Powered-By');
        $response->headers->remove('X-Powered-By');

        return $response;
    }
}