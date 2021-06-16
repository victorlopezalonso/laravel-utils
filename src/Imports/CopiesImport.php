<?php

namespace Victorlopezalonso\LaravelUtils\Imports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Victorlopezalonso\LaravelUtils\Classes\Copy;

class CopiesImport implements ToCollection
{
    public function collection(Collection $rows)
    {
        Copy::fromArray($rows);
    }
}
