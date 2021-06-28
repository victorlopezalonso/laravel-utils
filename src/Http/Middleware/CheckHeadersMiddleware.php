<?php

namespace Victorlopezalonso\LaravelUtils\Http\Middleware;

use Closure;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;
use Victorlopezalonso\LaravelUtils\Classes\Config;
use Victorlopezalonso\LaravelUtils\Classes\Headers;

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

        // TODO check minimum config version depending on OS
        // switch(Headers::getOs()) {
        // throw_if(
        //     config('config.property')
        //     Config::appVersionIsOutdated(),
        //     new ApiVersionOutdatedException()
        // );
        // }


        return $next($request);
    }
}
