<?php
namespace Tests\Feature;
use App\Models\CheckIn;use App\Models\Place;use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
class AdminDashboardTest extends TestCase {
 use RefreshDatabase;
 public function test_dashboard_is_private_to_verified_administrators(): void { $this->seed();$traveler=User::factory()->create(['email_verified_at'=>now()]);$unverified=User::factory()->unverified()->create(['role'=>'admin']);$this->actingAs($traveler)->get(route('admin.dashboard'))->assertForbidden();$this->actingAs($unverified)->get(route('admin.dashboard'))->assertRedirect(route('verification.notice')); }
 public function test_dashboard_consolidates_real_operational_metrics_and_links(): void { $this->seed();$admin=User::factory()->create(['role'=>'admin','email_verified_at'=>now()]);$traveler=User::factory()->create(['email_verified_at'=>now()]);$place=Place::where('publication_status','published')->first();CheckIn::create(['user_id'=>$traveler->id,'place_id'=>$place->id,'visited_on'=>today(),'status'=>'pending','evidence_method'=>'declaration','verification_consent_at'=>now()]);$this->actingAs($admin)->get(route('admin.dashboard'))->assertOk()->assertSee('Panel administrativo')->assertSee('1 pendientes')->assertSee('Catálogo editorial')->assertSee('Postal semanal')->assertSee(route('admin.moderation.index')); }
}
