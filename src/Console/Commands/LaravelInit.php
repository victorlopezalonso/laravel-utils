<?php

namespace Victorlopezalonso\LaravelUtils\Console\Commands;

use Exception;
use Illuminate\Support\Facades\App;
use Victorlopezalonso\LaravelUtils\Console\Command;

class LaravelInit extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'laravel:init';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Install the project';

    /**
     * Execute the console command.
     *
     * @throws Exception
     */
    public function handle()
    {
        if (!$this->welcome()) {
            return;
        }

        $this->generateAppKey();

        $this->askForConfiguration();

        if ($this->confirm('Do you want to set up supervisor?')) {
            $this->createSupervisorFiles();
        }

        if ($this->confirm('Do you want to set up supervisor for websockets?')) {
            $this->createSupervisorFilesForSockets();
        }

        $this->setUpAdminAccount();

        $this->storageLink();
    }

    /**
     * Welcome message.
     */
    protected function welcome()
    {
        system('clear');
        $this->error('This process will delete any existing configuration');

        if (!$this->confirm('Are you sure?')) {
            return false;
        }

        $this->comment('Attempting to install the project...');

        return true;
    }

    /**
     * Generate the app key and hash.
     *
     * @throws Exception
     */
    protected function generateAppKey()
    {
        if (!env('APP_KEY')) {
            $this->info('Generating app key...');
            $this->call('key:generate');
        }

        if (!env('APP_HASH')) {
            $this->info('Generating app secret...');
            $hash = base64_encode(random_bytes(10));
            $this->updateEnvironmentFile(['APP_HASH' => $hash]);
        }
    }

    /**
     * Set the database configuration and updates the .env file.
     */
    protected function askForConfiguration()
    {
        $config = [];

        do {
            $this->info('Database configuration.');

            $config['APP_ENV'] = $this->choice('Environment', config('laravel-utils.environments'), config('laravel-utils.environments.local'));

            $config['APP_DEBUG'] = config('constants.environments.production') !== $config['APP_ENV'];

            $config['APP_NAME'] = '"' . $this->ask('Name of the app') . '"';
            $config['APP_URL'] = $this->ask('Public url');

            $config['DB_CONNECTION'] = $this->choice('Database driver', [
                'mysql' => 'MySQL/MariaDB',
                'pgsql' => 'PostgreSQL',
                'sqlsrv' => 'SQL Server',
                'sqlite-e2e' => 'SQLite',
            ], 'mysql');

            if ('sqlite-e2e' === $config['DB_CONNECTION']) {
                $config['DB_DATABASE'] = $this->ask('Absolute path to the DB file');
            } else {
                $config['DB_HOST'] = $this->ask('DB host', 'localhost');
                $config['DB_PORT'] = $this->ask('DB port', '3306');
                $config['DB_DATABASE'] = $this->ask('DB name');
                $config['DB_USERNAME'] = $this->ask('DB user', 'root');
                $config['DB_PASSWORD'] = (string)$this->secret('DB password', false);
            }

            $config['MAIL_FROM_NAME'] = (string)$this->ask('Mail from name', $config['APP_NAME']);
            $config['MAIL_FROM_ADDRESS'] = $this->ask('Mail from address');
            $config['MAIL_DRIVER'] = $this->ask('Mail driver', 'smtp');
            $config['MAIL_HOST'] = $this->ask('Mail host', 'smtp.mailtrap.io');
            $config['MAIL_PORT'] = $this->ask('Mail port', '2525');
            $config['MAIL_ENCRYPTION'] = $this->choice('Mail encryption', [
                'ssl',
                'tls',
                'null',
            ], 'tls');

            $config['MAIL_USERNAME'] = (string)$this->ask('Mail username', 'e474163324d701');
            $config['MAIL_PASSWORD'] = (string)$this->secret('Mail password', 'f9badaecb12876');

            $this->table(array_keys($config), [$config]);
        } while (!$this->confirm('Proceed with this configuration?'));

        //Set the config so that the next DB attempt uses refreshed credentials
        config([
            'database.default' => $config['DB_CONNECTION'],
            "database.connections.{$config['DB_CONNECTION']}.host" => $config['DB_HOST'],
            "database.connections.{$config['DB_CONNECTION']}.port" => $config['DB_PORT'],
            "database.connections.{$config['DB_CONNECTION']}.database" => $config['DB_DATABASE'],
            "database.connections.{$config['DB_CONNECTION']}.username" => $config['DB_USERNAME'],
            "database.connections.{$config['DB_CONNECTION']}.password" => $config['DB_PASSWORD'],
        ]);

        $this->updateEnvironmentFile($config);

        $this->info('Migrating database');
        $this->call('migrate:fresh', ['--force' => true]);

        $this->info('Seeding initial data');
        $this->call('db:seed', ['--force' => true]);
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
        if (App::environment() === 'local') {
            return;
        }
        $this->createSupervisorConfigFile($this->getAppNameWithEnvironment(), 'queue:work --tries=100');
    }

    /**
     * Create the supervisor .conf file and start to listen for websockets.
     */
    protected function createSupervisorFilesForSockets()
    {
        if (App::environment() === 'local') {
            return;
        }
        $this->createSupervisorConfigFile('websockets', 'websockets:serve');
    }

    /**
     * Set up the admin account.
     */
    protected function setUpAdminAccount()
    {
        $this->call('laravel:create-admin-user');
    }

    /**
     * Create the public link to the storage/public folder.
     */
    protected function storageLink()
    {
        $this->call('storage:link');
    }
}
