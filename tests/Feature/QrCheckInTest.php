<?php
namespace Tests\Feature;
use App\Models\CheckIn;
use App\Models\Place;
use App\Models\PlaceQrCode;
use App\Models\Review;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
class QrCheckInTest extends TestCase {
    use RefreshDatabase;
    private function codeFor(Place $place,string $secret='physical-secret'): PlaceQrCode { return PlaceQrCode::create(['place_id'=>$place->id,'public_id'=>'123e4567-e89b-12d3-a456-426614174000','token_hash'=>hash_hmac('sha256',$secret,(string)config('app.key')),'expires_at'=>now()->addDay()]); }
    public function test_active_physical_qr_verifies_visit_and_related_review_once(): void {
        $this->seed();$user=User::factory()->create();$place=Place::first();$code=$this->codeFor($place);$before=$place->verified_visits_count;
        Review::create(['user_id'=>$user->id,'place_id'=>$place->id,'rating'=>5,'title'=>'Gran experiencia','body'=>'Una experiencia suficientemente detallada para esta prueba comunitaria.','status'=>'published']);
        $url=route('qr.confirm',['publicId'=>$code->public_id,'secret'=>'physical-secret']);
        $this->actingAs($user)->post($url)->assertRedirect(route('passport.show'));
        $this->assertDatabaseHas('check_ins',['user_id'=>$user->id,'place_id'=>$place->id,'status'=>'verified','evidence_method'=>'qr']);
        $this->assertTrue(Review::first()->is_visit_verified);$this->assertSame($before+1,$place->fresh()->verified_visits_count);
        $this->post($url)->assertRedirect();$this->assertSame($before+1,$place->fresh()->verified_visits_count);$this->assertSame(1,CheckIn::count());$this->assertDatabaseCount('passport_stamps',1);
    }
    public function test_invalid_or_expired_qr_is_rejected(): void {
        $this->seed();$place=Place::first();$code=$this->codeFor($place);
        $this->get(route('qr.show',['publicId'=>$code->public_id,'secret'=>'wrong']))->assertNotFound();
        $code->update(['expires_at'=>now()->subMinute()]);$this->get(route('qr.show',['publicId'=>$code->public_id,'secret'=>'physical-secret']))->assertNotFound();
    }
}
