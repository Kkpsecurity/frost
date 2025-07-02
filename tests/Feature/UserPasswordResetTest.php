<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Auth\Notifications\ResetPassword;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Password;
use Tests\TestCase;

class UserPasswordResetTest extends TestCase
{

    protected function getStudentUser()
    {
        // Prepare and delete any existing user
        $userData = [
            'fname' => 'Test',
            'lname' => 'User',
            'email' => 'testuser@example.com',
            'password' => bcrypt('correct-password'), // Hash password for user creation
            'avatar' => '',
            'use_gravatar' => false,
        ];

        User::where('email', $userData['email'])->delete();

        // Create the user
        $user = User::create($userData);

        // Return the User model instance
        return $user;
    }


    /** @test */
    public function it_sends_a_password_reset_link_when_requested_with_a_valid_email()
    {
        \Notification::fake();

        $user = $this->getStudentUser();
        $this->post('/password/email', ['email' => $user->email]);

        \Notification::assertSentTo($user, ResetPassword::class);
    }


    /**
     * A basic feature test example.
     *
     * @return void
     */
    /** @test */
    public function it_fails_to_send_a_password_reset_link_for_an_unregistered_email()
    {
        \Notification::fake();

        $this->post('/password/email', ['email' => 'notexisting@example.com']);

        \Notification::assertNothingSent();
    }

    /** @test */
    public function a_user_can_reset_password_successfully()
    {
        $user = $this->getStudentUser();
        $token = Password::broker()->createToken($user);

        $response = $this->post('/password/reset', [
            'token' => $token,
            'email' => $user->email,
            'password' => 'newpassword',
            'password_confirmation' => 'newpassword',
        ]);

        $this->assertTrue(\Hash::check('newpassword', $user->fresh()->password));
    }

    /** @test */
    public function an_expired_password_reset_link_cannot_be_used()
    {
        $user = $this->getStudentUser();
        $expiredToken = 'someexpiredtoken'; // Simulate an expired token. Implement token expiration logic as needed.

        $response = $this->post('/password/reset', [
            'token' => $expiredToken,
            'email' => $user->email,
            'password' => 'newpassword',
            'password_confirmation' => 'newpassword',
        ]);

        // Assert response or user's password was not changed.
        $this->assertFalse(\Hash::check('newpassword', $user->fresh()->password));
    }

}
