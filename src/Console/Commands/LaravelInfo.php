<?php

namespace Victorlopezalonso\LaravelUtils\Console\Commands;

use Exception;
use Illuminate\Support\Facades\Crypt;
use Victorlopezalonso\LaravelUtils\Console\Command;

class LaravelInfo extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'laravel:info';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'List of project variables';

    /**
     * Execute the console command.
     *
     * @throws Exception
     */
    public function handle()
    {
        $params = [
            "URL" => env("APP_URL"),
            "x-api-key" => env("APP_KEY"),
            "app_hash" => env("APP_HASH"),
            "deploy_URL" => env("APP_URL") . '/deploy/?key=' . Crypt::encrypt(env("APP_KEY")),
        ];

        $this->list('ℹ️', env('APP_NAME') . ' | ' . env("APP_ENV"), $params);
    }
}
