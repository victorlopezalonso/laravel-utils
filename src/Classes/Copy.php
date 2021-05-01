<?php

namespace Victorlopezalonso\LaravelUtils\Classes;

use Illuminate\Support\Facades\App;

class Copy
{
    private static function copies()
    {
        if (!$json = file_get_contents(resource_path() . '/lang/' . App::getLocale() . '.json')) {
            return [];
        }

        return json_decode($json, JSON_PRETTY_PRINT);
    }

    private static function filterBy($needle)
    {
        $copies = self::copies();

        return array_filter($copies, function ($key) use ($needle) {
            return strpos($key, $needle) === 0;
        }, ARRAY_FILTER_USE_KEY);
    }

    public static function get($type = null)
    {
        return $type ? self::filterBy($type) : self::copies();
    }

    public static function server()
    {
        return self::get(env('SERVER_COPY_KEY') ?? 'SERVER');
    }

    public static function client()
    {
        return self::get(env('CLIENT_COPY_KEY') ?? 'CLIENT');
    }

    public static function admin()
    {
        return self::get(env('ADMIN_COPY_KEY') ?? 'ADMIN');
    }
}
