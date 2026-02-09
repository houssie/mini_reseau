<?php

namespace app\middlewares;

use Flight;

class SecurityHeadersMiddleware
{
    public function __invoke()
    {
        // Set security headers
        Flight::response()->header('X-Content-Type-Options', 'nosniff');
        Flight::response()->header('X-Frame-Options', 'DENY');
        Flight::response()->header('X-XSS-Protection', '1; mode=block');
        Flight::response()->header('Referrer-Policy', 'strict-origin-when-cross-origin');

        // CSP Header with nonce
        $nonce = Flight::get('csp_nonce');
        if ($nonce) {
            // Note: 'unsafe-eval' is required for Alpine.js to work
            $csp = "default-src 'self'; script-src 'self' 'unsafe-inline' 'unsafe-eval'; style-src 'self' 'unsafe-inline'; img-src 'self' data: https:; font-src 'self' https://fonts.gstatic.com; connect-src 'self'";
            Flight::response()->header('Content-Security-Policy', $csp);
        }

        // CORS headers for API
        if (strpos(Flight::request()->url, '/api/') === 0) {
            Flight::response()->header('Access-Control-Allow-Origin', '*');
            Flight::response()->header('Access-Control-Allow-Methods', 'GET, POST, PUT, DELETE, OPTIONS');
            Flight::response()->header('Access-Control-Allow-Headers', 'Content-Type, Authorization, X-Requested-With');
        }
    }
}