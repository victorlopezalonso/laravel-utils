<?php

namespace Victorlopezalonso\LaravelUtils\Http\Middleware;

use Closure;
use Exception;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;
use Victorlopezalonso\LaravelUtils\Classes\Headers;
use Victorlopezalonso\LaravelUtils\Exceptions\ApiException;

class CheckHeadersMiddleware
{
    public function handle($request, Closure $next)
    {
        $validator = Validator::make(Headers::asArray(), [
            Headers::getKeyName('x-api-key') => [
                'required',
                Rule::in([env('APP_KEY')])
            ],
            Headers::getKeyName('os') => [
                'required',
                Rule::in([
                    config('laravel-utils.os.android'),
                    config('laravel-utils.os.ios'),
                    config('laravel-utils.os.web')])
                ],
            Headers::getKeyName('app-version') => 'required',
        ]);

        if ($validator->fails()) {
            throw new ValidationException($validator);
        }

        $minimumVersions = [
            config('laravel-utils.os.android') => config('config.androidVersion'),
            config('laravel-utils.os.ios') => config('config.iosVersion'),
            config('laravel-utils.os.web') => config('config.webVersion'),
        ];

        $minimumVersion = $minimumVersions[Headers::getOs()];

        throw_if(
            version_compare(Headers::getAppVersion(), $minimumVersion) === -1,
            new ApiException('server.version_update_required', 426)
        );

        return $next($request);
    }
}
