<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;

class AdminLoginTest extends TestCase
{
    protected function createAdminUser()
    {
        $email = 'admintest@example.com';
        User::where('email', $email)->delete();

        return User::create([
            'fname' => 'Admin',
            'lname' => 'Test',
            'email' => $email,
            'password' => bcrypt('admin-pass'),
            'role_id' => 2, // Admin role
            'is_active' => true,
        ]);
    }

    public function test_admin_can_login_and_access_dashboard()
    {
        $user = $this->createAdminUser();

        $response = $this->post('/admin/login', [
            'email' => $user->email,
            'password' => 'admin-pass',
        ]);

        // Following the login the app should redirect to the admin dashboard
        $response->assertRedirect('/admin');

        // Now visit the admin status endpoint and assert admin guard is set
        $status = $this->get('/admin/status')->decodeResponseJson();
        $this->assertTrue($status['is_admin_guard_authenticated'] || $status['is_web_guard_authenticated']);
    }
}
