<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class SetLocale
{
    /**
     * Handle an incoming request.
     *
     * Sets the application locale based on the URL structure.
     * - Routes with /tr prefix: Turkish
     * - All other routes: English (default)
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Check if the route starts with /tr
        $path = $request->path();

        if (str_starts_with($path, 'tr/') || $path === 'tr') {
            app()->setLocale('tr');
        } else {
            app()->setLocale('en');
        }

        return $next($request);
    }
}
