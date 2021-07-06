<?php

namespace Victorlopezalonso\LaravelUtils\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Foundation\Auth\User;
use Illuminate\Support\Facades\Hash;

class LaravelCreateAdminUser extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'laravel:create-admin-user';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a new Admin user';

    /**
     * Create a new command instance.
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     */
    public function handle()
    {
        system('clear');
        $this->info("Let's create a new admin account.");

        $name = $this->ask('Name');
        $email = $this->ask('Email address');

        $role = $this->choice('Set the user role', config('laravel-utils.roles'), config('laravel-utils.roles.root'));

        do {
            $password = $this->secret('Password');
            $confirmation = $this->secret('Again, just to make sure');

            if ($confirmation !== $password) {
                $this->error('That doesn\'t match. Let\'s try again.');
            }
        } while ($confirmation !== $password);

        $password = encryptWithAppSecret($password);

        User::unguard();

        User::create([
            'name' => $name,
            'email' => $email,
            'password' => Hash::make($password),
            'role' => $role,
            'email_verified_at' => now(),
        ]);

        User::reguard();

        $this->info('Admin user created!');
    }
}
