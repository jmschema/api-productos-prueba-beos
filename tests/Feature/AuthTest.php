<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AuthTest extends TestCase{
    use RefreshDatabase;

    public function test_user_can_login_with_valid_credentials():void{
        $user = User::factory()->create();
        $this->postJson('/api/login', [
            'email' => $user->email,
            'password' => 'password',
        ])->assertOk()->assertJsonStructure(['token', 'token_type']);
    }

    public function test_login_fails_with_invalid_credentials():void{
        $user = User::factory()->create();

        $this->postJson('/api/login', [
            'email' => $user->email,
            'password' => 'wrong-password',
        ])->assertUnauthorized();
    }

    public function test_protected_routes_require_authentication():void{
        $this->getJson('/api/products')->assertUnauthorized();
    }

    public function test_user_can_logout_and_token_is_revoked():void{
        $user = User::factory()->create();
        $token = $user->createToken('test')->plainTextToken;

        $this->withToken($token)->postJson('/api/logout')->assertNoContent();

        $this->assertDatabaseCount('personal_access_tokens', 0);
    }
}