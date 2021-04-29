<?php

namespace Victorlopezalonso\LaravelUtils;

use Illuminate\Support\Facades\Facade;

/**
 * @see \Victorlopezalonso\LaravelUtils\Skeleton\SkeletonClass
 */
class LaravelUtilsFacade extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return 'laravel-utils';
    }
}
