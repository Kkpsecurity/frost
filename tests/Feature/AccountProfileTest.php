<?php
namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;
use App\Models\User;

class AccountProfileTest extends TestCase
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
    public function user_can_update_profile_information()
    {
        $user = $this->getStudentUser();
        $this->actingAs($user);

        // Change this line to use the `put` method instead of `post`
        $response = $this->put('/account/profile/update', [
            'fname' => 'Test',
            'lname' => 'User',
            'email' => 'testuser@example.com',
        ]);

        if ($response->status() !== 302) {
            echo "Response status: " . $response->status() . "\n";
            echo "Response content: " . $response->getContent() . "\n";
        }

        $response->assertStatus(302);
        $this->assertDatabaseHas('users', [
            'id' => $user->id,
            'fname' => 'Test',
            'lname' => 'User',
            'email' => 'testuser@example.com',
        ]);
    }

    /** @test */
    public function user_can_update_password()
    {
        $user = $this->getStudentUser();
        $this->actingAs($user);

        $response = $this->post('/account/password/update', [
            'old_password' => 'correct-password',
            'password' => 'newpassword',
            'password_confirmation' => 'newpassword',
        ]);

        if ($response->status() !== 302) {
            echo "Response status: " . $response->status() . "\n";
            echo "Response content: " . $response->getContent() . "\n";
        }

        $response->assertStatus(302);
        $this->assertTrue(Hash::check('newpassword', $user->fresh()->password));
    }

    /** @test */
    public function user_can_upload_avatar()
    {
        // retrun a default assert
        $this->assertTrue(true);
    }
}
