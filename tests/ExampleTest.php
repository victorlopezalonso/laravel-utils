<?php

namespace Victorlopezalonso\LaravelUtils\Tests;

use Orchestra\Testbench\TestCase;
use Victorlopezalonso\LaravelUtils\LaravelUtilsServiceProvider;

class ExampleTest extends TestCase
{
    protected function getPackageProviders($app)
    {
        return [LaravelUtilsServiceProvider::class];
    }

    /** @test */
    public function true_is_true()
    {
        $this->assertTrue(true);
    }
}
