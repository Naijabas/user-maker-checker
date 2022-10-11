<?php

namespace Http\Controllers\API;

use App\Models\User;
use Tests\TestCase;
use App\Http\Controllers\API\AuthController;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Auth;

class AuthControllerTest extends TestCase
{
    /**
     * Test if email and password fields are required(Assert for the status code and Json data)
     * @return void
     */
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

    /**
     * Test if user will log in successfully(Assert for the status code and Json structure)
     * @return void
     */
    public function testUserLoginSuccessfully()
    {
        $user = ['email' => 'superadmin1@admin.com', 'password' => 'password'];
        $this->json('POST', 'api/login', $user)
            ->assertStatus(200) //Check if the status code is 200
            ->assertJsonStructure([
                'success',
                'data' => [
                    'token',
                    'email',
                    ],
                'message'
            ]);
    }


    /**
     * Test if register endpoint works(Assert for the status code and Json structure)
     * @return void
     */
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

    /**
     * Test if name, email and password fields are required(Assert for the status code and Json data)
     * @return void
     */
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

    /**
     * Test if password field is required(Assert for the status code and Json data)
     * @return void
     */
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

    /**
     * Test if password matches the confirmation password(Assert for the status code and Json data)
     * @return void
     */
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

    /**
     * Test if user is logged in normally
     * @return void
     */
    public function testUserIsLoggedOutProperly()
    {
        $user = User::factory()->create(['email' => 'myusedr@admin.com', 'password' => 'password']);
        $token = $user->createToken('Logged In')->plainTextToken;
        $headers = ['Authorization' => "Bearer $token"];
        $user = User::find($user->id);

        $this->json('post', '/api/logout', [], $headers)->assertStatus(204);
        $this->assertEquals(null, $user->plainTextToken);
    }
}
