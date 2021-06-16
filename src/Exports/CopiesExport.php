<?php

namespace Victorlopezalonso\LaravelUtils\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Victorlopezalonso\LaravelUtils\Classes\Copy;

class CopiesExport implements FromCollection
{
    public function collection()
    {
        $languages = config('laravel-utils.languages');
        
        $headers = array_merge(['key'], $languages);

        $collection = collect([$headers, array_values(Copy::toArray())]);

        // dd($collection);

        return $collection;
    }
}
