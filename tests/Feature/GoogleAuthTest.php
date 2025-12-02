<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Socialite\Facades\Socialite;
use Mockery;

class GoogleAuthTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        
        // Set up Google OAuth configuration for testing
        config([
            'services.google.client_id' => 'test-client-id',
            'services.google.client_secret' => 'test-client-secret',
            'services.google.redirect' => 'http://localhost:8000/api/auth/google/callback',
        ]);
    }

    /**
     * Test redirect to Google returns redirect response
     */
    public function test_redirect_to_google_returns_redirect_response()
    {
        $response = $this->get('/api/auth/google/redirect');
        
        // Should redirect to Google OAuth page
        $this->assertTrue(
            $response->isRedirect() || $response->status() === 302,
            'Expected redirect response'
        );
    }

    /**
     * Test Google callback creates new user
     */
    public function test_google_callback_creates_new_user()
    {
        // Mock Google user data
        $googleUser = Mockery::mock('Laravel\Socialite\Two\User');
        $googleUser->shouldReceive('getId')
            ->andReturn('123456789');
        $googleUser->shouldReceive('getEmail')
            ->andReturn('testuser@gmail.com');
        $googleUser->shouldReceive('getName')
            ->andReturn('Test User');
        $googleUser->shouldReceive('getAvatar')
            ->andReturn('https://example.com/avatar.jpg');
        
        // Mock properties for backward compatibility
        $googleUser->id = '123456789';
        $googleUser->name = 'Test User';
        $googleUser->email = 'testuser@gmail.com';
        $googleUser->avatar = 'https://example.com/avatar.jpg';

        // Mock Socialite
        Socialite::shouldReceive('driver->stateless->user')
            ->andReturn($googleUser);

        // Make request to callback
        $response = $this->get('/api/auth/google/callback');

        // Assert response
        $response->assertStatus(200);
        $response->assertJsonStructure([
            'status',
            'message',
            'user' => [
                'id',
                'name',
                'email',
                'google_id',
                'avatar',
                'role',
            ],
            'authorization' => [
                'token',
                'type',
            ],
        ]);

        // Assert user was created
        $this->assertDatabaseHas('users', [
            'email' => 'testuser@gmail.com',
            'google_id' => '123456789',
            'role' => 'student',
        ]);
    }

    /**
     * Test Google callback links existing user
     */
    public function test_google_callback_links_existing_user()
    {
        // Create existing user without google_id
        $existingUser = User::create([
            'name' => 'Existing User',
            'email' => 'existing@gmail.com',
            'password' => bcrypt('password'),
            'role' => 'student',
        ]);

        // Mock Google user data with same email
        $googleUser = Mockery::mock('Laravel\Socialite\Two\User');
        $googleUser->shouldReceive('getId')
            ->andReturn('987654321');
        $googleUser->shouldReceive('getEmail')
            ->andReturn('existing@gmail.com');
        $googleUser->shouldReceive('getName')
            ->andReturn('Existing User');
        $googleUser->shouldReceive('getAvatar')
            ->andReturn('https://example.com/avatar.jpg');
        
        $googleUser->id = '987654321';
        $googleUser->name = 'Existing User';
        $googleUser->email = 'existing@gmail.com';
        $googleUser->avatar = 'https://example.com/avatar.jpg';

        // Mock Socialite
        Socialite::shouldReceive('driver->stateless->user')
            ->andReturn($googleUser);

        // Make request to callback
        $response = $this->get('/api/auth/google/callback');

        // Assert response
        $response->assertStatus(200);

        // Assert user was updated with google_id
        $this->assertDatabaseHas('users', [
            'id' => $existingUser->id,
            'email' => 'existing@gmail.com',
            'google_id' => '987654321',
        ]);

        // Assert no new user was created
        $this->assertEquals(1, User::count());
    }

    /**
     * Test Google callback returns JWT token
     */
    public function test_google_callback_returns_jwt_token()
    {
        // Mock Google user data
        $googleUser = Mockery::mock('Laravel\Socialite\Two\User');
        $googleUser->shouldReceive('getId')
            ->andReturn('token-test-123');
        $googleUser->shouldReceive('getEmail')
            ->andReturn('tokentest@gmail.com');
        $googleUser->shouldReceive('getName')
            ->andReturn('Token Test User');
        $googleUser->shouldReceive('getAvatar')
            ->andReturn('https://example.com/avatar.jpg');
        
        $googleUser->id = 'token-test-123';
        $googleUser->name = 'Token Test User';
        $googleUser->email = 'tokentest@gmail.com';
        $googleUser->avatar = 'https://example.com/avatar.jpg';

        // Mock Socialite
        Socialite::shouldReceive('driver->stateless->user')
            ->andReturn($googleUser);

        // Make request to callback
        $response = $this->get('/api/auth/google/callback');

        // Assert response has token
        $response->assertStatus(200);
        $response->assertJsonStructure([
            'authorization' => [
                'token',
                'type',
            ],
        ]);

        $data = $response->json();
        $this->assertNotEmpty($data['authorization']['token']);
        $this->assertEquals('bearer', $data['authorization']['type']);
    }

    /**
     * Test Google config validation
     */
    public function test_google_config_validation_fails_when_missing()
    {
        // Clear Google OAuth configuration
        config([
            'services.google.client_id' => null,
            'services.google.client_secret' => null,
            'services.google.redirect' => null,
        ]);

        $response = $this->get('/api/auth/google/redirect');

        // Should return error
        $response->assertStatus(500);
        $response->assertJson([
            'status' => 'error',
            'message' => 'Google OAuth configuration error',
        ]);
    }

    /**
     * Test email domain validation
     */
    public function test_email_domain_validation_blocks_unauthorized_domains()
    {
        // Set allowed domains
        config([
            'services.google.allowed_domains' => 'alloweddomain.com,university.edu',
        ]);

        // Mock Google user with unauthorized domain
        $googleUser = Mockery::mock('Laravel\Socialite\Two\User');
        $googleUser->shouldReceive('getId')
            ->andReturn('blocked-user-123');
        $googleUser->shouldReceive('getEmail')
            ->andReturn('user@unauthorized.com');
        $googleUser->shouldReceive('getName')
            ->andReturn('Blocked User');
        $googleUser->shouldReceive('getAvatar')
            ->andReturn('https://example.com/avatar.jpg');
        
        $googleUser->id = 'blocked-user-123';
        $googleUser->name = 'Blocked User';
        $googleUser->email = 'user@unauthorized.com';
        $googleUser->avatar = 'https://example.com/avatar.jpg';

        // Mock Socialite
        Socialite::shouldReceive('driver->stateless->user')
            ->andReturn($googleUser);

        // Make request to callback
        $response = $this->get('/api/auth/google/callback');

        // Should return 403 Forbidden
        $response->assertStatus(403);
        $response->assertJson([
            'status' => 'error',
            'message' => 'Email domain not allowed',
        ]);

        // Assert user was not created
        $this->assertDatabaseMissing('users', [
            'email' => 'user@unauthorized.com',
        ]);
    }

    /**
     * Test email domain validation allows authorized domains
     */
    public function test_email_domain_validation_allows_authorized_domains()
    {
        // Set allowed domains
        config([
            'services.google.allowed_domains' => 'alloweddomain.com,university.edu',
        ]);

        // Mock Google user with authorized domain
        $googleUser = Mockery::mock('Laravel\Socialite\Two\User');
        $googleUser->shouldReceive('getId')
            ->andReturn('allowed-user-123');
        $googleUser->shouldReceive('getEmail')
            ->andReturn('student@university.edu');
        $googleUser->shouldReceive('getName')
            ->andReturn('Allowed User');
        $googleUser->shouldReceive('getAvatar')
            ->andReturn('https://example.com/avatar.jpg');
        
        $googleUser->id = 'allowed-user-123';
        $googleUser->name = 'Allowed User';
        $googleUser->email = 'student@university.edu';
        $googleUser->avatar = 'https://example.com/avatar.jpg';

        // Mock Socialite
        Socialite::shouldReceive('driver->stateless->user')
            ->andReturn($googleUser);

        // Make request to callback
        $response = $this->get('/api/auth/google/callback');

        // Should succeed
        $response->assertStatus(200);
        $response->assertJson([
            'status' => 'success',
        ]);

        // Assert user was created
        $this->assertDatabaseHas('users', [
            'email' => 'student@university.edu',
        ]);
    }

    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }
}
