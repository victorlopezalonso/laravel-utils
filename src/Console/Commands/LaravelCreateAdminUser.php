<?php

namespace App\Console\Commands;

use Carbon\Carbon;
use App\Models\User;
use Illuminate\Console\Command;

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
        $permissionsList = [
            'Root'       => config('constants.users.root'),
            'Admin'      => config('constants.users.admin'),
            'Consultant' => config('constants.users.consultant'),
        ];

        system('clear');
        $this->info("Let's create a new admin account.");

        $name = $this->ask('Name');
        $email = $this->ask('Email address');

        $permissions = $this->choice('Set the user permissions', [
            config('constants.users.root')       => 'Root',
            config('constants.users.admin')      => 'Admin',
            config('constants.users.consultant') => 'Consultant',
        ], config('constants.users.root'));

        $permissions = $permissionsList[$permissions];

        do {
            $password = $this->secret('Password');
            $confirmation = $this->secret('Again, just to make sure');

            if ($confirmation !== $password) {
                $this->error('That doesn\'t match. Let\'s try again.');
            }
        } while ($confirmation !== $password);

        $password = encryptWithAppSecret($password);

        User::create([
            'user_name'   => 'ROOT_'.$name,
            'name'        => $name,
            'email'       => $email,
            'password'    => $password,
            'is_admin'    => true,
            'permissions' => $permissions,
            'email_verified_at'    => Carbon::now()->timestamp,
            'verified' => true,
        ]);

        $this->info('Admin user created!');
    }
}
