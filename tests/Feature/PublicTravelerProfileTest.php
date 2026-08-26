<?php
namespace Tests\Feature;
use App\Models\CheckIn;
use App\Models\Place;
use App\Models\User;
use App\Services\VerifyCheckIn;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
class PublicTravelerProfileTest extends TestCase {
    use RefreshDatabase;
    public function test_profile_is_private_by_default_and_requires_explicit_consent(): void {
        $this->seed();$user=User::factory()->create();
        $this->assertFalse($user->is_profile_public);
        $this->actingAs($user)->put(route('profile.public.update'),['is_profile_public'=>'1','public_name_mode'=>'alias','public_alias'=>'Ruta Azul'])->assertSessionHasErrors('public_consent');
        $this->assertFalse($user->fresh()->is_profile_public);
    }
    public function test_traveler_can_publish_selected_verified_information_and_disable_it(): void {
        $this->seed();$user=User::factory()->create();$place=Place::first();$checkIn=CheckIn::create(['user_id'=>$user->id,'place_id'=>$place->id,'visited_on'=>today(),'status'=>'pending','evidence_method'=>'self_reported','verification_consent_at'=>now()]);app(VerifyCheckIn::class)->handle($checkIn,'manual');
        $this->actingAs($user)->put(route('profile.public.update'),['is_profile_public'=>'1','public_name_mode'=>'alias','public_alias'=>'Ruta Azul','show_public_achievements'=>'1','public_consent'=>'1'])->assertSessionHasNoErrors();
        $user->refresh();$this->assertNotNull($user->public_profile_id);
        $this->get(route('travelers.public',$user->public_profile_id))->assertOk()->assertSee('Ruta Azul')->assertSee('Explorador inicial')->assertDontSee($user->email)->assertDontSee($place->name);
        $this->actingAs($user)->put(route('profile.public.update'),['public_name_mode'=>'alias','public_alias'=>'Ruta Azul'])->assertSessionHasNoErrors();
        $this->get(route('travelers.public',$user->public_profile_id))->assertNotFound();
    }
    public function test_public_stamp_visibility_is_respected(): void {
        $this->seed();$user=User::factory()->create();$place=Place::first();$checkIn=CheckIn::create(['user_id'=>$user->id,'place_id'=>$place->id,'visited_on'=>today(),'status'=>'pending','evidence_method'=>'self_reported','verification_consent_at'=>now()]);app(VerifyCheckIn::class)->handle($checkIn,'manual');
        $this->actingAs($user)->put(route('profile.public.update'),['is_profile_public'=>'1','public_name_mode'=>'real','show_public_stamps'=>'1','public_consent'=>'1']);$user->refresh();
        $this->get(route('travelers.public',$user->public_profile_id))->assertOk()->assertSee($user->name)->assertSee($place->name)->assertDontSee($user->email);
    }
}
