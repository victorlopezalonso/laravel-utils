<?php

namespace Victorlopezalonso\LaravelUtils\Classes;

use Illuminate\Support\Facades\App;

class Copy
{
    private static function copies()
    {
        $path = resource_path() . '/lang/' . App::getLocale() . '.json';

        if (!file_exists($path)) {
            return [];
        }

        return json_decode(file_get_contents($path), JSON_PRETTY_PRINT);
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
        return self::get(env('SERVER_COPY_KEY') ?? 'server.');
    }

    public static function client()
    {
        return self::get(env('CLIENT_COPY_KEY') ?? 'client.');
    }

    public static function admin()
    {
        return self::get(env('ADMIN_COPY_KEY') ?? 'admin.');
    }

    public static function add($language, $newCopies)
    {
        $path = resource_path() . '/lang/' . $language . '.json';
        $copies = [];

        if (file_exists($path)) {
            $copies = json_decode(file_get_contents($path), JSON_PRETTY_PRINT);
        }

        $updatedCopies = array_merge($copies, $newCopies);

        ksort($updatedCopies);

        file_put_contents($path, json_encode($updatedCopies, JSON_PRETTY_PRINT));
    }
}
