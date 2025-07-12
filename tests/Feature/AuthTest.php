<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use PHPUnit\Framework\Attributes\Test;
use Illuminate\Support\Facades\Hash;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class AuthTest extends TestCase
{
    /**
     * A basic feature test example.
     */
     use RefreshDatabase;

    #[Test]
    public function user_can_register_successfully()
    {
        $response = $this->postJson('/api/register', [
            'name' => 'John Doe',
            'username' => 'johndoe',
            'email' => 'john@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ]);

        $response->assertStatus(201)
                 ->assertJsonStructure([
                     'user' => ['id', 'name', 'username', 'email'],
                     'token',
                 ]);

        $this->assertDatabaseHas('users', [
            'username' => 'johndoe',
            'email' => 'john@example.com',
        ]);
    }

    #[Test]
    public function registration_fails_with_invalid_data()
    {
        $response = $this->postJson('/api/register', [
            'name' => '',
            'username' => 'jd', // too short
            'email' => 'not-an-email',
            'password' => '123', // too short
            'password_confirmation' => 'wrong',
        ]);

        $response->assertStatus(400)
                 ->assertJsonStructure([
                     'message'
                 ]);
    }

    #[Test]
    public function user_can_login_with_correct_credentials()
    {
        $user = User::create([
            'name' => 'Test User',
            'username' => 'testuser',
            'email' => 'test@example.com',
            'password' => Hash::make('securepassword'),
        ]);

        $response = $this->postJson('/api/login', [
            'username' => 'testuser',
            'password' => 'securepassword',
        ]);

        $response->assertStatus(200)
                 ->assertJsonStructure([
                     'user' => ['id', 'name', 'username', 'email'],
                     'token',
                 ]);
    }

    #[Test]
    public function login_fails_with_invalid_credentials()
    {
        $user = User::create([
            'name' => 'Test User',
            'username' => 'testuser',
            'email' => 'test@example.com',
            'password' => Hash::make('securepassword'),
        ]);

        $response = $this->postJson('/api/login', [
            'username' => 'testuser',
            'password' => 'wrongpassword',
        ]);

        $response->assertStatus(422)
                 ->assertJson([
                     'message' => 'Invalid credentials',
                 ]);
    }

    #[Test]
    public function login_fails_when_user_does_not_exist()
    {
        $response = $this->postJson('/api/login', [
            'username' => 'nonexistent',
            'password' => 'password123',
        ]);

        $response->assertStatus(400);
    }
    
}
