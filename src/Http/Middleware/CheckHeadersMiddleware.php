<?php

namespace Victorlopezalonso\LaravelUtils\Http\Middleware;

use Victorlopezalonso\LaravelUtils\Classes\Headers;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Closure;

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

        // throw_if(
        //     Config::first()->appVersionIsOutdated(),
        //     new ApiVersionOutdatedException()
        // );

        return $next($request);
    }
}