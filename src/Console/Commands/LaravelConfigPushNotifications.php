<?php

namespace Victorlopezalonso\LaravelUtils\Console\Commands;

use Exception;
use Victorlopezalonso\LaravelUtils\Console\Command;

class LaravelConfigPushNotifications extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'laravel:config-push-notifications';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Set up the push notification keys';

    /**
     * Execute the console command.
     *
     * @throws Exception
     */
    public function handle()
    {
        $this->askForConfiguration();

        $this->info('Push notification credentials have been updated!');
    }

    protected function askForConfiguration()
    {
        $config = [];

        system('clear');

        do {
            $this->info('Push notifications');

            $config['PUSHER_APP_ID'] = $this->ask('Pusher app id');
            $config['PUSHER_APP_KEY'] = $this->ask('Pusher app key');

            $this->table(array_keys($config), [$config]);
        } while (!$this->confirm('Proceed with this configuration?'));

        $this->updateEnvironmentFile($config);
    }
}
