<?php

namespace Victorlopezalonso\LaravelUtils\Classes;

use App\Exceptions\ApiVersionOutdatedException;
use App\Models\Config;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

/**
 * Class Headers.
 */
class Headers
{
    /**
     * Return the headers sent to the service as an array.
     *
     * @return array
     */
    public static function asArray()
    {
        return [
            config('headers.api_key')     => self::getApiKey(),
            config('headers.language')    => self::getLanguage(),
            config('headers.os')          => self::getOs(),
            config('headers.app_version') => self::getAppVersion(),
        ];
    }

    /**
     * Return the apikey header param.
     *
     * @return string
     */
    public static function getApiKey()
    {
        return request()->header(config('headers.api_key'));
    }

    /**
     * Return the language header param.
     *
     * @return string
     */
    public static function getLanguage()
    {
        $language = request()->header(config('headers.language'));

        if (!$language || !\in_array($language, Config::languages(), true)) {
            return config('languages.default');
        }

        return $language;
    }

    /**
     * Return the appVersion header param.
     *
     * @return string
     */
    public static function getAppVersion()
    {
        return request()->header(config('headers.app_version')) ?? '0.0.0';
    }

    /**
     * Return the os header param.
     *
     * @return string
     */
    public static function getOs()
    {
        return request()->header(config('headers.os'));
    }

    /**
     * Check if the os is Android.
     *
     * @return bool
     */
    public static function isAndroid()
    {
        return strtolower(config('os.android.name')) === strtolower(request()->header(config('headers.os')));
    }

    /**
     * Check if the os is iOS.
     *
     * @return bool
     */
    public static function isIos()
    {
        return strtolower(config('os.ios.name')) === strtolower(request()->header(config('headers.os')));
    }

    /**
     * Return the os param as an integer.
     *
     * @return null|int
     */
    public static function getOsAsInteger()
    {
        if (self::isAndroid()) {
            return config('os.android.value');
        }

        if (self::isIos()) {
            return config('os.ios.vaulue');
        }

        return config('os.other.value');
    }

    /**
     * Check the required headers.
     *
     * @throws \Throwable
     */
    public static function checkHeaders()
    {
        if (!config('flags.check_headers_middlware')) {
            return;
        }

        $validator = Validator::make(self::asArray(), [
            config('headers.api_key')     => ['required', Rule::in([env('APP_KEY')])],
            config('headers.os')          => ['required', Rule::in([
                config('os.android.name'),
                config('os.ios.name'),
            ])],
            config('headers.app_version') => 'required',
        ]);

        if ($validator->fails()) {
            throw new ValidationException($validator);
        }

        throw_if(
            Config::first()->appVersionIsOutdated(),
            new ApiVersionOutdatedException()
        );
    }
}
