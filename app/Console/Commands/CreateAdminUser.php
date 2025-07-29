<?php

declare(strict_types=1);

namespace App\Console\Commands;

/**
 * @file CreateAdminUser.php
 * @brief Command to create an admin user.
 * @details This command creates a new admin user with a specified role and credentials.
 */

use Illuminate\Support\Str;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Hash;

use App\Models\User;
use App\Helpers\PgTk;


class CreateAdminUser extends Command
{

    protected $signature   = 'command:create_admin_user';
    protected $description = 'Create Admin User';


    public function handle(): int
    {

        $user_id    = 5000;
        $role_id    = 3;
        $fname      = 'Support';
        $lname      = 'Manager';
        $email      = 'support@floridaonlinesecuritytraining.com';
        $password   = Str::random(24);


        if (User::where('email', $email)->first()) {
            $this->error("Email exists: {$email}");
            return 1;
        }


        //
        //
        //


        if (! ($user_id ?? false)) {
            $user_id = User::where('role_id', '<', 5)
                ->where('id', '<', config('define.support.manager_user_id'))
                ->orderBy('id', 'DESC')
                ->first()
                ->id + 1;
        }


        $User = User::forceCreate([

            'id'                => $user_id,
            'role_id'           => $role_id,
            'fname'             => $fname,
            'lname'             => $lname,
            'email'             => $email,
            'email_verified_at' => PgTk::now(),
            'password'          => Hash::make($password),
            'remember_token'    => Str::random(60),

        ]);


        //
        //
        //


        if (! app()->environment('production')) {
            $filename = storage_path('devel') . "/{$email}.txt";
            $fh = fopen($filename, 'w');
            fwrite($fh, "Name:      {$User->fullname()}\n");
            fwrite($fh, "Email:     {$email}\n");
            fwrite($fh, "Password:  {$password}\n");
            fclose($fh);
            $this->line("Wrote {$filename}");
            $this->line('');
        }


        //
        //
        //


        $this->info('Created User');
        $this->line(print_r($User->toArray(), true));
        $this->line("Email:     {$User->email}");
        $this->line("Password:  {$password}");

        return 0;
    }
}
