<?php

namespace Victorlopezalonso\LaravelUtils\Classes;

use Illuminate\Http\UploadedFile;
use Maatwebsite\Excel\Facades\Excel;
use Victorlopezalonso\LaravelUtils\Exports\CopiesExport;
use Victorlopezalonso\LaravelUtils\Imports\CopiesImport;

class Copy
{
    private static function getTypeKey($type)
    {
        switch ($type) {
            case 'client':
                return env('CLIENT_COPY_KEY') ?? 'client.';
            case 'server':
                return env('SERVER_COPY_KEY') ?? 'server.';
            case 'admin':
                return env('ADMIN_COPY_KEY') ?? 'admin.';
            default:
                return $type;
        }
    }

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
        return self::get(self::getTypeKey('server'));
    }

    /**
     * Return client translations using the header language
     *
     * @param string $type
     * @return array
     */
    public static function client()
    {
        return self::get(self::getTypeKey('client'));
    }

    /**
     * Return admin translations using the header language
     *
     * @param string $type
     * @return array
     */
    public static function admin()
    {
        return self::get(self::getTypeKey('admin'));
    }

    /**
     * Return translated key in all the app languages
     *
     * @param string $key
     * @param array $replacements
     * @return array
     */
    private static function inAllLanguages($key, $replacements = [])
    {
        $languages = config('laravel-utils.languages');
        $copies = [];

        foreach ($languages as $language) {
            $copies[$language] = trans($key, $replacements, $language);
        }

        return $copies;
    }

    /**
     * Return translated server key in all the app languages
     *
     * @param string $key
     * @param array $replacements
     * @return array
     */
    public static function serverInAllLanguages($key, $replacements = [])
    {
        return self::inAllLanguages(self::getTypeKey('server') . $key, $replacements);
    }

    /**
     * Return translated admin key in all the app languages
     *
     * @param string $key
     * @param array $replacements
     * @return array
     */
    public static function adminInAllLanguages($key, $replacements = [])
    {
        return self::inAllLanguages(self::getTypeKey('admin') . $key, $replacements);
    }

    /**
     * Return translated client key in all the app languages
     *
     * @param string $key
     * @param array $replacements
     * @return array
     */
    public static function clientInAllLanguages($key, $replacements = [])
    {
        return self::inAllLanguages(self::getTypeKey('client') . $key, $replacements);
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

        Config::incrementCopiesVersion();
    }

    private static function addInAllLanguages($newCopies)
    {
        $languages = config('laravel-utils.languages');

        foreach ($languages as $language) {
            self::add($language, [$newCopies['key'] => $newCopies[$language]]);
        }
    }

    public static function addServerCopyInAllLanguages($copy)
    {
        $copy['key'] = self::getTypeKey('server').$copy['key'];

        self::addInAllLanguages($copy);
    }

    public static function addClientCopyInAllLanguages($copy)
    {
        $copy['key'] = self::getTypeKey('client').$copy['key'];

        self::addInAllLanguages($copy);
    }

    public static function addAdminCopyInAllLanguages($copy)
    {
        $copy['key'] = self::getTypeKey('admin').$copy['key'];

        self::addInAllLanguages($copy);
    }

    private static function filterArrayByType($array, $type)
    {
        return array_filter($array, function ($key) use ($type) {
            return strpos($key, self::getTypeKey($type)) === 0;
        }, ARRAY_FILTER_USE_KEY);
    }

    private static function filterLanguageArrayByAllFields($array, $language, $needle)
    {
        return array_filter($array, function ($copy, $key) use ($language, $needle) {
            return strpos($key, $needle) !== false
                || strpos($copy[$language], $needle) !== false;
        }, ARRAY_FILTER_USE_BOTH);
    }

    public static function toArray($type = null, $needle = null)
    {
        $languages = config('laravel-utils.languages');
        $copiesArray = [];

        foreach ($languages as $language) {
            $path = resource_path() . '/lang/' . $language . '.json';

            if (!file_exists($path)) {
                return;
            }

            $copies = json_decode(file_get_contents($path), JSON_PRETTY_PRINT);

            if ($type) {
                $copies = self::filterArrayByType($copies, $type);
            }

            if ($needle) {
                $copies = self::filterLanguageArrayByAllFields($copies, $language, $needle);
            }

            foreach ($copies as $key => $value) {
                $copiesArray[$key]['key'] = $key;
                $copiesArray[$key][$language] = $value;
            }
        }
        return $copiesArray;
    }

    public static function clientArray($needle = null)
    {
        return self::toArray('client', $needle);
    }

    public static function serverArray($needle = null)
    {
        return self::toArray('server', $needle);
    }

    public static function adminArray($needle = null)
    {
        return self::toArray('admin', $needle);
    }

    public static function searchInAllLanguages($needle)
    {
        return self::toArray(null, $needle);
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

    public static function versionNeedsToBeUpdated($copiesVersion = null)
    {
        return (int)(config('config.copiesVersion') ?? 1) > (int)($copiesVersion ?? 0);
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
