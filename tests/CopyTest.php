<?php

namespace Victorlopezalonso\LaravelUtils\Tests;

use Orchestra\Testbench\TestCase;
use Illuminate\Support\Facades\Config;
use Victorlopezalonso\LaravelUtils\Classes\Copy;

class CopyTest extends TestCase
{
    protected $copy;

    protected function setUp(): void
    {
        parent::setUp();

        Config::set('laravel-utils', include(__DIR__.'/../config/config.php'));

        $this->copy = [
            'key' => 'copy_in_all_languages',
            'es' => 'Texto en español',
            'en' => 'Text in english'
        ];
    }

    /** @test */
    public function it_creates_a_copy()
    {
        $testValue = [
            'es' => 'Esto es un valor de prueba',
            'en' => 'This is a test value',
        ];

        Copy::add('es', ['server.test_key' => $testValue['es']]);
        Copy::add('en', ['server.test_key' => $testValue['en']]);

        $this->assertEquals($testValue['es'], trans('server.test_key', [], 'es'));
        $this->assertEquals($testValue['en'], trans('server.test_key', [], 'en'));
    }

    /** @test */
    public function it_creates_a_server_copy_in_multiple_languages()
    {
        Copy::addClientCopyInAllLanguages($this->copy);

        $this->assertEquals($this->copy['es'], trans('server.copy_in_all_languages', [], 'es'));
        $this->assertEquals($this->copy['es'], trans('server.copy_in_all_languages', [], 'es'));
    }

    /** @test */
    public function it_creates_a_client_copy_in_multiple_languages()
    {
        Copy::addClientCopyInAllLanguages($this->copy);

        $this->assertEquals($this->copy['es'], trans('client.copy_in_all_languages', [], 'es'));
        $this->assertEquals($this->copy['es'], trans('client.copy_in_all_languages', [], 'es'));
    }

    /** @test */
    public function it_creates_an_admin_copy_in_multiple_languages()
    {
        Copy::addAdminCopyInAllLanguages($this->copy);

        $this->assertEquals($this->copy['es'], trans('admin.copy_in_all_languages', [], 'es'));
        $this->assertEquals($this->copy['es'], trans('admin.copy_in_all_languages', [], 'es'));
    }

    /** @test */
    public function it_returns_a_server_copy_in_multiple_languages()
    {
        Copy::addServerCopyInAllLanguages($this->copy);

        $copy = Copy::serverInAllLanguages($this->copy['key']);

        $this->assertEquals($this->copy['es'], $copy['es']);
        $this->assertEquals($this->copy['en'], $copy['en']);
    }

    /** @test */
    public function it_returns_a_client_copy_in_multiple_languages()
    {
        Copy::addClientCopyInAllLanguages($this->copy);

        $copy = Copy::clientInAllLanguages($this->copy['key']);

        $this->assertEquals($this->copy['es'], $copy['es']);
        $this->assertEquals($this->copy['en'], $copy['en']);
    }

    /** @test */
    public function it_returns_an_admin_copy_in_multiple_languages()
    {
        Copy::addAdminCopyInAllLanguages($this->copy);

        $copy = Copy::adminInAllLanguages($this->copy['key']);

        $this->assertEquals($this->copy['es'], $copy['es']);
        $this->assertEquals($this->copy['en'], $copy['en']);
    }

    /** @test */
    public function it_returns_an_admin_copy_filtered()
    {
        Copy::addAdminCopyInAllLanguages($this->copy);

        $results = Copy::searchInAllLanguages($this->copy['key']);
        $this->assertTrue((bool)count($results));

        $results = Copy::searchInAllLanguages($this->copy['es']);
        $this->assertTrue((bool)count($results));

        $results = Copy::searchInAllLanguages($this->copy['en']);
        $this->assertTrue((bool)count($results));

        $result = Copy::searchInAllLanguages('notexistingtext');
        $this->assertFalse((bool)count($result));
    }
}
