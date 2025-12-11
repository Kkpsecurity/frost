<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\User;

class RegistrationTest extends TestCase
{
    /**
     * Test successful user registration with custom user attributes.
     *
     * @return void
     */
    public function test_user_can_register_successfully_with_custom_attributes()
    {

        User::unsetEventDispatcher(); // Temporarily disable all observers

        $user = User::where('email', 'testuser@example.com')->first();
        if ($user) {
            $user->forceDelete(); // Remove the user if it already exists
        }

        User::setEventDispatcher(new \Illuminate\Events\Dispatcher()); // Re-enable observers


        // Arrange: Prepare data for a new user considering custom model attributes
        $userData = [
            'fname' => 'Test',
            'lname' => 'User',
            'email' => 'testuser@example.com',
            'password' => 'password',
            'password_confirmation' => 'password',
            'avatar' => '', // Assuming a default or provided avatar value
            'use_gravatar' => false,
        ];

        // Act: Send a POST request to the registration endpoint
        $response = $this->post('/register', $userData);

        // Assert: Basic checks (redirection, authentication, etc.)
        $response->assertRedirect('/pages'); // Adjust redirect assertion based on your app's flow
        $this->assertAuthenticated();

        // Optionally, you can test for the presence of the user in the database
        $this->assertDatabaseHas('users', ['email' => $userData['email']]);
    }

    /**
     * Test registration fails with validation errors for invalid inputs.
     * @return void
     */
    public function test_registration_with_validation_errors()
    {
        // Arrange: Invalid registration data
        $invalidUserData = [
            'fname' => '', // Missing first name should fail validation
            'lname' => '', // Missing last name should fail validation
            'email' => 'invalid-email', // Invalid email format should fail validation
            'password' => 'short', // Too short password should fail validation
            'password_confirmation' => 'short', // Mismatch or short confirmation should fail validation            ]
        ];

        // Act: Attempt to register with invalid data
        $response = $this->post('/register', $invalidUserData);

        // Assert: Expect redirection (typically back to the registration form)
        $response->assertRedirect();

        // Assert: Check for specific validation errors
        $response->assertSessionHasErrors(['fname', 'lname', 'email', 'password']);

        // Optionally, you can test for the absence of user creation
        $this->assertDatabaseMissing('users', ['email' => $invalidUserData['email']]);
    }

    /**
     * Test that the system prevents duplicate user registrations.
     * @return void
     */
    public function test_system_prevents_duplicate_user_registrations()
    {
        // Arrange: Register a user with a specific email
        $email = 'duplicate@example.com';

        $userData = [
            'fname' => 'First',
            'lname' => 'User',
            'email' => $email,
            'password' => 'password123',
            'password_confirmation' => 'password123',
            'avatar' => '',
            'use_gravatar' => false,

        ];

        $this->post('/register', $userData);

        // Act: Attempt to register another user with the same email
        $secondUserData = [
            'fname' => 'Second',
            'lname' => 'User',
            'email' => $email, // Same email as the first user
            'password' => 'differentpassword',
            'password_confirmation' => 'differentpassword',
            'avatar' => '',
            'use_gravatar' => false,

        ];

        $secondResponse = $this->post('/register', $secondUserData);

        // Assert: The second registration attempt should fail due to the duplicate email
        $secondResponse->assertSessionHasErrors(['email']);

        // Optionally check if only one user exists with that email to ensure no duplicates were created
        $this->assertEquals(1, User::where('email', $email)->count());
    }
}
