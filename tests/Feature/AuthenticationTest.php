<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Auth\Notifications\VerifyEmail;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;
use Tests\TestCase;

class AuthenticationTest extends TestCase
{
    use RefreshDatabase;

    public function test_a_visitor_can_register_after_accepting_legal_terms(): void
    {
        Notification::fake();

        $response = $this->post('/registro', [
            'name' => 'Ana Viajera',
            'email' => 'ana@example.com',
            'password' => 'Turismo2026',
            'password_confirmation' => 'Turismo2026',
            'terms' => '1',
        ]);

        $response->assertRedirect(route('verification.notice'));
        $this->assertAuthenticated();
        $this->assertDatabaseHas('users', [
            'email' => 'ana@example.com',
            'role' => 'traveler',
            'terms_version' => config('app.terms_version'),
        ]);
        Notification::assertSentTo(User::where('email', 'ana@example.com')->first(), VerifyEmail::class);
    }

    public function test_registration_requires_acceptance_of_terms(): void
    {
        $this->post('/registro', [
            'name' => 'Ana Viajera',
            'email' => 'ana@example.com',
            'password' => 'Turismo2026',
            'password_confirmation' => 'Turismo2026',
        ])->assertSessionHasErrors('terms');

        $this->assertGuest();
    }

    public function test_a_registered_user_can_log_in(): void
    {
        $user = User::factory()->create(['password' => 'Turismo2026']);

        $this->post('/ingresar', [
            'email' => $user->email,
            'password' => 'Turismo2026',
        ])->assertRedirect(route('profile'));

        $this->assertAuthenticatedAs($user);
    }

    public function test_an_unverified_user_cannot_open_the_profile(): void
    {
        $user = User::factory()->unverified()->create();

        $this->actingAs($user)->get('/mi-perfil')->assertRedirect(route('verification.notice'));
    }

    public function test_local_environment_offers_a_safe_development_verification_link(): void
    {
        $this->app->detectEnvironment(fn () => 'local');
        $user = User::factory()->unverified()->create();

        $this->actingAs($user)
            ->get('/verificar-correo')
            ->assertOk()
            ->assertSee('Verificar correo en desarrollo');
    }
}
