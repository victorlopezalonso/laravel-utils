<?php

namespace Victorlopezalonso\LaravelUtils\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\App;
use Victorlopezalonso\LaravelUtils\Classes\Headers;

class LocalizationMiddleware
{

    /**
     * Check if the requested language exists in language
     * @return bool
     */
    public function languageExists()
    {
        if (!Headers::header('language') || !env('LANGUAGES')) {
            return false;
        }

        return in_array(Headers::header('language'), explode(',', env('LANGUAGES')));
    }

    /**
     * Handle an incoming request.
     *
     * @param \Illuminate\Http\Request $request
     * @param \Closure $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        $locale = $this->languageExists() ? Headers::header('language') : config('app.locale');

        App::setLocale($locale);

        return $next($request);
    }
}
