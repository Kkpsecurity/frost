<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Admin;
use Illuminate\Support\Facades\Hash;

class CreateTestAdmin extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'admin:create-test {email? : The admin email to update password for}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create or update admin user password for authentication testing';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $email = $this->argument('email');

        if ($email) {
            // Update specific admin user
            $admin = Admin::where('email', $email)->first();

            if (!$admin) {
                $this->error("No admin user found with email: {$email}");
                $this->info('Available admin emails:');
                $admins = Admin::select('email')->get();
                foreach ($admins as $adminUser) {
                    $this->line("  - {$adminUser->email}");
                }
                return;
            }
        } else {
            // Show all admin users and let user choose
            $admins = Admin::select('id', 'fname', 'lname', 'email')->get();

            if ($admins->isEmpty()) {
                $this->error('No admin users found in the database!');
                return;
            }

            $this->info('Available admin users:');
            $this->table(['ID', 'First Name', 'Last Name', 'Email'],
                $admins->map(function($admin) {
                    return [$admin->id, $admin->fname, $admin->lname, $admin->email];
                })->toArray()
            );

            $email = $this->ask('Enter the email of the admin to update password for');
            $admin = Admin::where('email', $email)->first();

            if (!$admin) {
                $this->error("No admin user found with email: {$email}");
                return;
            }
        }

        // Update password to something known
        $admin->password = Hash::make('password');
        $admin->save();

        $this->info('Admin user password updated successfully!');
        $this->table(['ID', 'First Name', 'Last Name', 'Email'], [
            [$admin->id, $admin->fname, $admin->lname, $admin->email]
        ]);
        $this->info('Password: password');
        $this->info('You can now login at: http://frost.test/admin/login');
        $this->info('Or use the password reset feature at: http://frost.test/admin/password/reset');
    }
}
