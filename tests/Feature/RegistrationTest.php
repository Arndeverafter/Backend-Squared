<?php


namespace Tests\Feature\Auth;

use App\Models\User;
use App\Providers\RouteServiceProvider;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RegistrationTest extends TestCase
{
    use RefreshDatabase; // to remove the csrf Auth

    public function test_users_can_register_via_the_api()
    {
         $this->postJson('/register', [
            'name' => 'John Mnemonic',
            'email' => 'johhny@mnemonic.com',
            'phone' => '255789016235',
            'password' => 'password',
            'password_confirmation' => 'password',
            'user_type' => 'admin',
        ]);

        $this->assertAuthenticated();
        $user = User::latest()->first();

        expect(User::latest()->first())
            ->email->toContain("@")
            ->name->toBeString()->not->toBeEmpty()
            ->user_type->toEqual('user'); // Every registered user will have a default user_type Even if they pass it within the request
    }

    public function test_validation_of_inputs_when_registering()
    {
        $response = $this->withHeader('Accept','application/json')->postJson('/register', [
            'name' => 'John Mnemonic',
            'email' => 'johhnymnemonic',
            'phone' => '255789016235',
            'password' => 'password',
            'password_confirmation' => 'password',
            'user_type' => 'admin',
        ]);

        $response->assertUnprocessable(); // validation errors set
        expect($response)->assertJsonValidationErrorFor('email');
    }
}


