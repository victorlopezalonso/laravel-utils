<?php

namespace Victorlopezalonso\LaravelUtils\Classes;

use Illuminate\Http\UploadedFile;
use Maatwebsite\Excel\Facades\Excel;
use Victorlopezalonso\LaravelUtils\Exports\CopiesExport;
use Victorlopezalonso\LaravelUtils\Imports\CopiesImport;

class Copy
{
    private static function copies()
    {
        $path = resource_path() . '/lang/' . Headers::getLanguage() . '.json';

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

    /**
     * Return specified type translations using the header language
     *
     * @param string $type
     * @return array
     */
    public static function get($type = null)
    {
        return $type ? self::filterBy($type) : self::copies();
    }

    /**
     * Return server translations using the header language
     *
     * @param string $type
     * @return array
     */
    public static function server()
    {
        return self::get(env('SERVER_COPY_KEY') ?? 'server.');
    }

    /**
     * Return client translations using the header language
     *
     * @param string $type
     * @return array
     */
    public static function client()
    {
        return self::get(env('CLIENT_COPY_KEY') ?? 'client.');
    }

    /**
     * Return admin translations using the header language
     *
     * @param string $type
     * @return array
     */
    public static function admin()
    {
        return self::get(env('ADMIN_COPY_KEY') ?? 'admin.');
    }

    /**
     * Return translated key in all the app languages
     *
     * @param string $type
     * @return array
     */
    public static function inAllLanguages($key, $replacements = [])
    {
        $languages = config('laravel-utils.languages');
        $copies = [];

        foreach ($languages as $language) {
            $copies[$language] = trans($key, $replacements);
        }

        return $copies;
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

    public static function toArray()
    {
        $languages = config('laravel-utils.languages');
        $copiesArray = [];

        foreach ($languages as $language) {
            $path = resource_path() . '/lang/' . $language . '.json';

            if (!file_exists($path)) {
                return;
            }

            $copies = json_decode(file_get_contents($path), JSON_PRETTY_PRINT);

            foreach ($copies as $key => $value) {
                $copiesArray[$key]['key'] = $key;
                $copiesArray[$key][$language] = $value;
            }
        }
        return $copiesArray;
    }

    public static function toArrayWithHeaders()
    {
        $headers = array_merge(['key'], config('laravel-utils.languages'));

        return [$headers, array_values(self::toArray())];
    }

    public static function fromArray($rows)
    {
        $languages = config('laravel-utils.languages');

        unset($rows[0]);

        foreach ($languages as $pos => $language) {
            $copies = [];

            foreach ($rows as $row) {
                $key = $row[0];
                $value = $row[$pos + 1];

                $copies[$key] = $value;
            }

            self::add($language, $copies);
        }
    }

    public static function fromExcel(UploadedFile $file = null)
    {
        return Excel::import(new CopiesImport, $file ?? 'default_copies.xlsx');
    }

    public static function toExcel()
    {
        return Excel::store(new CopiesExport, 'copies_' . now()->toDateString() . '.xlsx');
    }

    public static function downloadToExcel()
    {
        return Excel::download(new CopiesExport, 'copies_' . now()->toDateString() . '.xlsx');
    }
}
