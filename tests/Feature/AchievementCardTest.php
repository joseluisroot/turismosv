<?php
namespace Tests\Feature;
use App\Models\Achievement;
use App\Models\CheckIn;
use App\Models\Place;
use App\Models\User;
use App\Services\VerifyCheckIn;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
class AchievementCardTest extends TestCase {
    use RefreshDatabase;
    public function test_traveler_can_open_a_card_only_for_an_earned_achievement(): void {
        $this->seed();$user=User::factory()->create();$place=Place::first();$checkIn=CheckIn::create(['user_id'=>$user->id,'place_id'=>$place->id,'visited_on'=>today(),'status'=>'pending','evidence_method'=>'self_reported','verification_consent_at'=>now()]);app(VerifyCheckIn::class)->handle($checkIn,'manual');$achievement=$user->achievements()->first();
        $this->actingAs($user)->get(route('passport.achievements.card',$achievement))->assertOk()->assertSee('Tu logro merece viajar')->assertSee('Descargar PNG')->assertSee($user->name)->assertSee($achievement->name);
    }
    public function test_an_unearned_or_unverified_card_is_not_exposed(): void {
        $this->seed();$achievement=Achievement::first();$verified=User::factory()->create();$this->actingAs($verified)->get(route('passport.achievements.card',$achievement))->assertNotFound();$unverified=User::factory()->unverified()->create();$this->actingAs($unverified)->get(route('passport.achievements.card',$achievement))->assertRedirect(route('verification.notice'));
    }
}
