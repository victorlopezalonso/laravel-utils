<?php

namespace Victorlopezalonso\LaravelUtils\Classes;

// use App\Exceptions\ApiVersionOutdatedException;
// use App\Models\Config;

use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

define('API_KEY', config('laravel-utils.headers.x-api-key'));
define('LANGUAGE', config('laravel-utils.headers.language'));
define('OS', config('laravel-utils.headers.os'));
define('APP_VERSION', config('laravel-utils.headers.app_version'));
define('LANGUAGES', config('laravel-utils.languages'));
define('DEFAULT_LANGUAGE', config('laravel-utils.default_language'));
define('OS_ANDROID', config('laravel-utils.os.android'));
define('OS_IOS', config('laravel-utils.os.ios'));
define('OS_WEB', config('laravel-utils.os.web'));

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
            API_KEY => self::getApiKey(),
            LANGUAGE => self::getLanguage(),
            OS => self::getOs(),
            APP_VERSION => self::getAppVersion(),
        ];
    }

    /**
     * Return the apikey header param.
     *
     * @return string
     */
    public static function getApiKey()
    {
        return request()->header(API_KEY);
    }

    /**
     * Return the language header param.
     *
     * @return string
     */
    public static function getLanguage()
    {
        $language = request()->header(LANGUAGE);

        if (!$language || !\in_array($language, LANGUAGES, true)) {
            return DEFAULT_LANGUAGE;
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
        return request()->header(APP_VERSION) ?? '0.0.0';
    }

    /**
     * Return the os header param.
     *
     * @return string
     */
    public static function getOs()
    {
        return strtolower(request()->header(OS));
    }

    /**
     * Check if the os is Android.
     *
     * @return bool
     */
    public static function isAndroid()
    {
        return strtolower(OS_ANDROID) === self::getOs();
    }

    /**
     * Check if the os is iOS.
     *
     * @return bool
     */
    public static function isIos()
    {
        return strtolower(OS_IOS) === self::getOs();
    }

    /**
     * Check if the os is iOS.
     *
     * @return bool
     */
    public static function isWeb()
    {
        return strtolower(OS_WEB) === self::getOs();
    }
}
