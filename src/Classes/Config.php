<?php

namespace Victorlopezalonso\LaravelUtils\Classes;

use Carbon\Carbon;

define('CONFIG_PATH', storage_path('/app/config.json'));

class Config
{
    private static function get()
    {
        if (!file_exists(CONFIG_PATH)) {
            return [];
        }

        return json_decode(file_get_contents(CONFIG_PATH), true);
    }

    /**
     * Initialize config.json using Laravel's config helper
     * Then you can access a value using config('config.property')
     *
     * @return void
     */
    public static function init()
    {
        config(['config' => self::get()]);
    }

    public static function put(array $properties)
    {
        $config = array_merge(self::get(), $properties);

        ksort($config);

        file_put_contents(CONFIG_PATH, json_encode($config, JSON_PRETTY_PRINT));
    }

    public static function needsToBeUpdated()
    {
        if (!request('copiesUpdatedAt')) {
            return true;
        }

        $copiesUpdatedAt = Carbon::createFromFormat('Y-m-d H:i:s', config('config.copiesUpdatedAt'));
        $requestCopiesUpdatedAt = Carbon::createFromFormat('Y-m-d H:i:s', request('copiesUpdatedAt'));

        return $copiesUpdatedAt->greaterThan($requestCopiesUpdatedAt);
    }
}
