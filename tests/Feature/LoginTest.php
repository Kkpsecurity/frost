<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\User;

class LoginTest extends TestCase
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


    /**
     * Test that a user can log in with correct credentials.
     */
    public function test_user_can_login_with_correct_credentials()
    {
        // Arrange: Prepare data for a new user considering custom model attributes
        $user = $this->getStudentUser();

        $loginResponse = $this->post('/login', [
            'email' => $user->email,
            'password' => 'correct-password', // Use the plaintext password
        ]);

       
        // Assert that the user is authenticated
        $this->assertAuthenticatedAs($user);
    }


    /**
     * Test login attempt with incorrect credentials.
     */
    public function test_login_with_incorrect_credentials()
    {
        $user = $this->getStudentUser();

        // Attempt to log in with incorrect password
        $response = $this->post('/login', [
            'email' => $user->email,
            'password' => 'wrong-password',
        ]);

        $response->assertSessionHasErrors(['email']); // Expect an error message for the email field    
    }


    /**
     * Test logout functionality.
     */
    public function test_logout_functionality()
    {
        $user = $this->getStudentUser();
        $this->actingAs($user);

        $response = $this->post('/logout');
        $response->assertRedirect('/');
    }

    /**
     * Test session handling after logout.
     */
    public function test_session_handling_after_logout()
    {
        $user = $this->getStudentUser();
        $this->actingAs($user)->post('/logout');

        // Attempt to access a route that requires authentication
        $response = $this->get('/account'); // Use a route that requires authentication
        $response->assertRedirect('/login'); // The user should be redirected to the login page
    }
}
