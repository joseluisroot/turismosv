<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class GoogleAnalyticsTest extends TestCase
{
    use RefreshDatabase;

    public function test_tag_is_available_only_on_public_guest_pages(): void
    {
        $this->seed();
        config(['analytics.enabled' => true, 'analytics.measurement_id' => 'G-75MT2GD44N']);
        $this->get(route('home'))->assertOk()->assertSee('data-analytics-id="G-75MT2GD44N"', false)
            ->assertDontSee('https://www.googletagmanager.com/gtag/js', false);
        $this->get(route('login'))->assertOk()->assertSee('data-analytics-id=""', false);
        $this->actingAs(User::factory()->create())->get(route('home'))->assertOk()
            ->assertSee('data-analytics-id=""', false);
    }

    public function test_disabled_or_invalid_configuration_does_not_enable_tracking(): void
    {
        $this->seed();
        config(['analytics.enabled' => false]);
        $this->get(route('home'))->assertSee('data-analytics-id=""', false);
        config(['analytics.enabled' => true, 'analytics.measurement_id' => 'invalid']);
        $this->get(route('home'))->assertSee('data-analytics-id=""', false);
    }
}
