<?php
namespace Tests\Feature;
use App\Models\CheckIn;
use App\Models\Place;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
class CheckInTest extends TestCase {
    use RefreshDatabase;
    public function test_verified_user_can_submit_a_pending_visit_without_inflating_verified_metrics(): void {
        $this->seed(); $user=User::factory()->create(); $place=Place::where('slug','el-tunco')->firstOrFail(); $verifiedBefore=$place->verified_visits_count;
        $payload=['visited_on'=>now()->toDateString(),'note'=>'Recorrí la playa durante la tarde.','verification_consent'=>'1'];
        $this->actingAs($user)->post(route('checkins.store',$place),$payload)->assertRedirect(route('places.show',$place));
        $this->assertDatabaseHas('check_ins',['user_id'=>$user->id,'place_id'=>$place->id,'status'=>'pending','evidence_method'=>'self_reported']);
        $this->assertSame($verifiedBefore,$place->fresh()->verified_visits_count);
        $this->post(route('checkins.store',$place),$payload)->assertRedirect();
        $this->assertSame(1,CheckIn::where('user_id',$user->id)->where('place_id',$place->id)->count());
    }
    public function test_unverified_user_cannot_submit_a_visit(): void {
        $this->seed(); $user=User::factory()->unverified()->create(); $place=Place::first();
        $this->actingAs($user)->post(route('checkins.store',$place),[])->assertRedirect(route('verification.notice'));
        $this->assertDatabaseMissing('check_ins',['user_id'=>$user->id,'place_id'=>$place->id]);
    }
    public function test_recent_date_and_verification_declaration_are_required(): void {
        $this->seed(); $user=User::factory()->create(); $place=Place::first();
        $this->actingAs($user)->post(route('checkins.store',$place),['visited_on'=>now()->subDays(31)->toDateString()])->assertSessionHasErrors(['visited_on','verification_consent']);
    }
}
