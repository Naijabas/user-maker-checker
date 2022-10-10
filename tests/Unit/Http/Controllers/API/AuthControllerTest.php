<?php

namespace Http\Controllers\API;

use Tests\TestCase;
use App\Http\Controllers\API\AuthController;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Auth;

class AuthControllerTest extends TestCase
{
    public function testRequireEmailAndLogin()
    {
        $this->json('POST', '/api/login')
            ->assertStatus(422)
            ->assertJson([
                'success' => false,
                'message' => 'Validation Error.',
                'errors' => [
                    'email' => ['The email field is required.'],
                    'password' => ['The password field is required.']
                ]
            ]);

    }

    public function testUserLoginSuccessfully()
    {
        $user = ['email' => 'superadmin1@admin.com', 'password' => 'password'];
        $this->json('POST', 'api/login', $user)
            ->assertStatus(200)
            ->assertJsonStructure([
                'success',
                'data' => [
                    'token',
                    'email',
                    ],
                'message'
            ]);
    }


    public function testRegisterSuccessfully()
    {
        $register = [
            'first_name' => 'UserTest',
            'last_name' => 'UserTest',
            'email' => 'user@test.com',
            'password' => 'testpass',
            'c_password' => 'testpass'
        ];

        $this->json('POST', 'api/register', $register)
            ->assertStatus(201)
            ->assertJsonStructure([
                'success',
                'data' => [
                    'token',
                    'email',
                ],
                'message'
            ]);
    }

    public function testRequireNameEmailAndPassword()
    {
        $this->json('POST', 'api/register')
            ->assertStatus(422)
            ->assertJson([
                'success' => false,
                'message' => 'Validation Error.',
                'errors' => [
                    'first_name' => ['The first name field is required.'],
                    'last_name' => ['The last name field is required.'],
                    'email' => ['The email field is required.'],
                    'password' => ['The password field is required.']
                ]
            ]);
    }

    public function testRequirePasswordConfirmation()
    {
        $register = [
            'first_name' => 'User',
            'last_name' => 'User',
            'email' => 'user@test.com',
            'password' => 'userpass'
        ];

        $this->json('POST', 'api/register', $register)
            ->assertStatus(422)
            ->assertJson([
                'success' => false,
                'message' => 'Validation Error.',
                'errors' => [
                    'c_password' => ['The confirmation password field is required.']
                ]
            ]);
    }

    public function testMatchPasswordConfirmation()
    {
        $register = [
            'first_name' => 'User',
            'last_name' => 'User',
            'email' => 'user@test.com',
            'password' => 'userpass',
            'c_password' => 'userpsass'
        ];

        $this->json('POST', 'api/register', $register)
            ->assertStatus(422)
            ->assertJson([
                'success' => false,
                'message' => 'Validation Error.',
                'errors' => [
                    'c_password' => ['The confirmation password and password must match.']
                ]
            ]);
    }
}
