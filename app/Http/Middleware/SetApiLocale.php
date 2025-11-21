<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class SetApiLocale
{
    /**
     * Handle an incoming request.
     *
     * Sets the application locale based on the request's Accept-Language header
     * or the 'locale' query parameter.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Priority: query parameter > Accept-Language header > default locale
        $locale = $request->query('locale')
            ?? $request->header('Accept-Language')
            ?? config('app.locale');

        // Validate locale (only allow configured locales)
        $availableLocales = ['tr', 'en']; // Add more as needed

        if (in_array($locale, $availableLocales)) {
            app()->setLocale($locale);
        }

        return $next($request);
    }
}
