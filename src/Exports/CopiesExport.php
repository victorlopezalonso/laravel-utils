<?php

namespace Victorlopezalonso\LaravelUtils\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Victorlopezalonso\LaravelUtils\Classes\Copy;

class CopiesExport implements FromCollection
{
    public function collection()
    {
        $collection = collect(Copy::toArrayWithHeaders());

        return $collection;
    }
}
