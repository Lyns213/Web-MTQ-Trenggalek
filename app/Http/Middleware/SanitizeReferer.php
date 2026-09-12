<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class SanitizeReferer
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    // public function handle(Request $request, Closure $next): Response
    // {
    //     return $next($request);
    // }

    public function handle(Request $request, Closure $next)
    {
        $referer = $request->header('referer');
        if ($referer) {
            $sanitizedReferer = filter_var($referer, FILTER_SANITIZE_URL);
            $request->headers->set('referer', $sanitizedReferer);
        }

        return $next($request);
    }
}
