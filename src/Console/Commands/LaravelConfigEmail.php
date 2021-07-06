<?php

namespace Victorlopezalonso\LaravelUtils\Console\Commands;

use Exception;
use Victorlopezalonso\LaravelUtils\Console\Command;

class LaravelConfigEmail extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'laravel:config-email';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Set up the email credentials for the app';

    /**
     * Execute the console command.
     *
     * @throws Exception
     */
    public function handle()
    {
        $this->askForConfiguration();

        $this->info('Email credentials have been updated!');
    }

    /**
     * Set the email configuration and updates the .env file.
     */
    protected function askForConfiguration()
    {
        $config = [];

        system('clear');

        do {
            $this->info('Email settings');

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

        $this->updateEnvironmentFile($config);
    }
}
