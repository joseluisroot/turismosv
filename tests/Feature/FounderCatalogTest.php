<?php

namespace Tests\Feature;

use App\Models\Department;
use App\Models\Place;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class FounderCatalogTest extends TestCase
{
    use RefreshDatabase;

    public function test_seed_creates_safe_national_founder_inventory(): void
    {
        $this->seed();
        $this->assertSame(14, Department::count());
        $this->assertSame(41, Place::where('is_founder_candidate', true)->count());
        $this->assertSame(35, Place::where('is_founder_candidate', true)->where('publication_status', 'draft')->count());
        $this->get('/explorar?q=Apaneca')->assertOk()->assertDontSee('Candidato del catálogo fundador');
    }

    public function test_only_admin_can_manage_founder_workflow(): void
    {
        $this->seed();
        $admin = User::factory()->create(['role' => 'admin', 'email_verified_at' => now()]);
        $traveler = User::factory()->create(['email_verified_at' => now()]);
        $place = Place::where('slug', 'apaneca')->firstOrFail();
        $this->actingAs($traveler)->get(route('admin.founder.index'))->assertForbidden();
        $this->actingAs($admin)->get(route('admin.founder.index'))->assertOk()->assertSee('14 departamentos representados')->assertSee('Apaneca');
        $this->actingAs($admin)->put(route('admin.founder.update', $place), [
            'founder_priority' => 'high', 'founder_stage' => 'contacting', 'founder_contact_status' => 'contacted',
            'founder_photo_status' => 'requested', 'founder_contact_name' => 'Administración local',
            'founder_contact_email' => 'contacto@example.com', 'founder_notes' => 'Primer acercamiento documentado.',
        ])->assertSessionHasNoErrors();
        $this->assertDatabaseHas('places', ['id' => $place->id, 'founder_stage' => 'contacting', 'founder_assigned_to' => $admin->id]);
    }

    public function test_founder_workflow_cannot_bypass_editorial_publication(): void
    {
        $this->seed();
        $admin = User::factory()->create(['role' => 'admin', 'email_verified_at' => now()]);
        $place = Place::where('slug', 'apaneca')->firstOrFail();
        $this->actingAs($admin)->put(route('admin.founder.update', $place), [
            'founder_priority' => 'medium', 'founder_stage' => 'published', 'founder_contact_status' => 'responded',
            'founder_photo_status' => 'authorized', 'founder_contact_name' => '', 'founder_contact_email' => '', 'founder_notes' => '',
        ])->assertSessionHasErrors('founder_stage');
        $this->assertSame('draft', $place->fresh()->publication_status);
    }
}
