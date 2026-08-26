<?php
namespace Tests\Feature;
use App\Models\CheckIn;
use App\Models\Place;
use App\Models\User;
use App\Services\VerifyCheckIn;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
class PassportTest extends TestCase {
    use RefreshDatabase;
    public function test_only_verified_visits_issue_unique_passport_stamps(): void {
        $this->seed();$user=User::factory()->create();$place=Place::first();
        $checkIn=CheckIn::create(['user_id'=>$user->id,'place_id'=>$place->id,'visited_on'=>today(),'status'=>'pending','evidence_method'=>'self_reported','verification_consent_at'=>now()]);
        $this->assertDatabaseCount('passport_stamps',0);$service=app(VerifyCheckIn::class);$first=$service->handle($checkIn,'manual',null,$user->id,'Prueba de aprobación');$second=$service->handle($checkIn->fresh(),'manual',null,$user->id,'Prueba repetida');
        $this->assertSame($first->id,$second->id);$this->assertDatabaseCount('passport_stamps',1);
        $this->assertDatabaseCount('point_transactions',2);$this->assertDatabaseCount('user_achievements',1);$this->assertSame(150,$user->fresh()->points_balance);
        $this->actingAs($user)->get(route('passport.show'))->assertOk()->assertSee($place->name)->assertSee($first->stamp_code)->assertSee('150 pts')->assertSee('Explorador inicial')->assertSee('lugares sellados');
    }
    public function test_unverified_user_cannot_open_passport(): void {
        $user=User::factory()->unverified()->create();$this->actingAs($user)->get(route('passport.show'))->assertRedirect(route('verification.notice'));
    }
    public function test_multiple_verified_places_unlock_progress_achievements_once(): void {
        $this->seed();$user=User::factory()->create();$places=Place::with('department')->get();$service=app(VerifyCheckIn::class);
        foreach($places->take(3) as $place){$checkIn=CheckIn::create(['user_id'=>$user->id,'place_id'=>$place->id,'visited_on'=>today(),'status'=>'pending','evidence_method'=>'self_reported','verification_consent_at'=>now()]);$service->handle($checkIn,'manual');}
        $this->assertDatabaseCount('passport_stamps',3);$this->assertDatabaseCount('user_achievements',3);$this->assertDatabaseCount('point_transactions',6);$this->assertSame(600,$user->fresh()->points_balance);
    }
    public function test_each_traveler_can_earn_the_same_achievement(): void {
        $this->seed();$place=Place::first();$service=app(VerifyCheckIn::class);
        foreach(User::factory()->count(2)->create() as $user){$checkIn=CheckIn::create(['user_id'=>$user->id,'place_id'=>$place->id,'visited_on'=>today(),'status'=>'pending','evidence_method'=>'self_reported','verification_consent_at'=>now()]);$service->handle($checkIn,'manual');$this->assertSame(150,$user->fresh()->points_balance);}
        $this->assertDatabaseCount('user_achievements',2);$this->assertDatabaseCount('point_transactions',4);
    }
}
