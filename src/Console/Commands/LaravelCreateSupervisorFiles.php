<?php

namespace Victorlopezalonso\LaravelUtils\Console\Commands;

use Exception;
use Victorlopezalonso\LaravelUtils\Console\Command;

class LaravelCreateSupervisorFiles extends Command
{
    protected $dir = '/etc/supervisor/conf.d/';

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'laravel:create-supervisor-files';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create supervisor config files';

    /**
     * Execute the console command.
     *
     * @throws Exception
     */
    public function handle()
    {
        system('clear');

        $dir = $this->ask("This will add supervisor config files into {$this->dir}", $this->dir);

        $this->createSupervisorFiles($dir);

        if ($this->confirm("Do you want to add sockets support? (this will add a websocket file for each environment)")) {
            $this->createSupervisorFilesForSockets();
        }
    }

    /**
     * Returns the app name concatenating the environment. Example: "My App" -> MyApp.Develop
     * @return string
     */
    protected function getAppNameWithEnvironment()
    {
        return preg_replace('/\s+/', '', env('APP_NAME')) . ucwords(env('APP_ENV'));
    }

    /**
     * Create the supervisor .conf file and start to listen.
     */
    protected function createSupervisorFiles()
    {
        $this->createSupervisorConfigFile($this->getAppNameWithEnvironment(), 'queue:work --tries=100');
    }

    /**
     * Create the supervisor .conf file and start to listen for websockets.
     */
    protected function createSupervisorFilesForSockets()
    {
        $this->createSupervisorConfigFile('websockets', 'websockets:serve');
    }
}
