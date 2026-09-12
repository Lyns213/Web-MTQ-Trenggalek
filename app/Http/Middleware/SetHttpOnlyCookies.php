<?php

// app/Http/Middleware/SetHttpOnlyCookies.php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Cookie;

class SetHttpOnlyCookies
{
    public function handle($request, Closure $next)
    {
        $response = $next($request);

        // Set HttpOnly flag for cookies
        $cookies = $response->headers->getCookies();
        foreach ($cookies as $cookie) {
            $cookie->setHttpOnly(true);
        }

        return $response;
    }
}
