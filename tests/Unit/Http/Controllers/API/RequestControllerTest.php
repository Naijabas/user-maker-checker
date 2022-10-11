<?php

namespace Http\Controllers\API;

use App\Models\User;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;
use App\Http\Controllers\API\AuthController;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Auth;

class RequestControllerTest extends TestCase
{

    /**
     * A feature test to get all approved requests
     *
     * @return void
     */
    public function test_get_all_approved_requests()
    {
        // Using Laravel Sanctum to handle the authentication
        Sanctum::actingAs( User::factory()->create() );
        $response = $this->json('GET', '/api/requests', [])->assertStatus(200)->assertJsonStructure(
            [
                'data'
            ]
        );

    }

    /**
     * A feature test to check if user is authenticated
     *
     * @return void
     */
    public function test_if_user_is_not_authenticated()
    {
        $response = $this->json('GET', '/api/requests/', [
            'first_name' => 'First',
            'last_name' => 'First',
            'email' => 'first@first.com',
            'password' => 'password'
        ])->assertStatus(401);

    }

    /**
     * A feature test to store requests
     *
     * @return void
     */
    public function test_store_requests()
    {
        // Using Laravel Sanctum to handle the authentication
        Sanctum::actingAs( User::factory()->create());
        $response = $this->json('POST', '/api/requests', [
            'first_name' => 'First',
            'last_name' => 'First',
            'email' => 'first@first.com',
            'password' => 'password'
        ])->assertStatus(200);

    }

    /**
     * A feature test to update requests
     *
     * @return void
     */
    public function test_update_requests()
    {
        // Using Laravel Sanctum to handle the authentication
        Sanctum::actingAs( User::factory()->create());
        $response = $this->json('PUT', '/api/requests/1', [
            'email' => 'first@first.com',
        ])->assertStatus(200);

    }

    /**
     * A feature test to update requests
     *
     * @return void
     */
    public function test_delete_requests()
    {
        // Using Laravel Sanctum to handle the authentication
        Sanctum::actingAs( User::factory()->create());
        $response = $this->json('DELETE', '/api/requests/1', [
            'id' =>    1,
        ])->assertStatus(200);

    }

    /**
     * A feature test to update requests
     *
     * @return void
     */
    public function test_if_i_can_approve_my_requests()
    {
        // Using Laravel Sanctum to handle the authentication
        Sanctum::actingAs( User::factory()->create());
        $response = $this->json('POST', '/api/request/1/approve', [
        ])->assertStatus(404);

    }

}
