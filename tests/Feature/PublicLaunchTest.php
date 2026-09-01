<?php

namespace Tests\Feature;

use App\Models\LaunchChecklistItem;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PublicLaunchTest extends TestCase
{
    use RefreshDatabase;

    public function test_only_admin_can_open_launch_control_center(): void
    {
        $this->seed();
        $traveler = User::factory()->create(['email_verified_at' => now()]);
        $admin = User::factory()->create(['role' => 'admin', 'email_verified_at' => now()]);

        $this->actingAs($traveler)->get(route('admin.launch.index'))->assertForbidden();
        $this->actingAs($admin)->get(route('admin.launch.index'))->assertOk()
            ->assertSee('Lista de lanzamiento')
            ->assertSee('Google y Facebook habilitado');
    }

    public function test_admin_completion_is_audited_and_can_be_reopened(): void
    {
        $this->seed();
        $admin = User::factory()->create(['role' => 'admin', 'email_verified_at' => now()]);
        $item = LaunchChecklistItem::where('key', 'mobile_qa')->firstOrFail();

        $this->actingAs($admin)->put(route('admin.launch.update', $item), [
            'is_completed' => '1',
            'evidence_notes' => 'Registro y pasaporte comprobados en dos teléfonos.',
        ])->assertSessionHasNoErrors();

        $item->refresh();
        $this->assertTrue($item->is_completed);
        $this->assertSame($admin->id, $item->completed_by);
        $this->assertNotNull($item->completed_at);

        $this->put(route('admin.launch.update', $item), ['evidence_notes' => 'Debe repetirse.']);
        $item->refresh();
        $this->assertFalse($item->is_completed);
        $this->assertNull($item->completed_by);
        $this->assertNull($item->completed_at);
    }
}
