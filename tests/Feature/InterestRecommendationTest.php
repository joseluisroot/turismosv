<?php
namespace Tests\Feature;
use App\Models\CheckIn;
use App\Models\Interest;
use App\Models\Place;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
class InterestRecommendationTest extends TestCase {
    use RefreshDatabase;
    public function test_verified_traveler_can_save_private_interests(): void {
        $this->seed();$user=User::factory()->create();$interests=Interest::take(2)->pluck('id')->all();
        $this->actingAs($user)->put(route('interests.update'),['interests'=>$interests])->assertRedirect(route('home'));
        $this->assertCount(2,$user->fresh()->interests);$this->assertNotNull($user->fresh()->interests_selected_at);
        $this->actingAs($user)->get(route('profile'))->assertOk()->assertDontSee(Interest::find($interests[0])->name);
    }
    public function test_recommendations_match_interests_and_exclude_visited_places(): void {
        $this->seed();$user=User::factory()->create();$interest=Interest::where('slug','playas-surf')->first();$user->interests()->attach($interest);$visited=Place::where('slug','el-tunco')->first();CheckIn::create(['user_id'=>$user->id,'place_id'=>$visited->id,'visited_on'=>today(),'status'=>'pending','evidence_method'=>'self_reported','verification_consent_at'=>now()]);
        $this->actingAs($user)->get(route('home'))->assertOk()->assertSee('Recomendado para ti')->assertSee('Los Cóbanos');
        $response=$this->actingAs($user)->get(route('home'));$section=substr($response->getContent(),strpos($response->getContent(),'recommendations-section'));$this->assertStringNotContainsString('El Tunco',$section);
    }
    public function test_unverified_traveler_cannot_edit_interests_and_selection_is_limited(): void {
        $this->seed();$unverified=User::factory()->unverified()->create();$this->actingAs($unverified)->get(route('interests.edit'))->assertRedirect(route('verification.notice'));
        $user=User::factory()->create();$this->actingAs($user)->put(route('interests.update'),['interests'=>Interest::pluck('id')->all()])->assertSessionHasErrors('interests');
    }
}
