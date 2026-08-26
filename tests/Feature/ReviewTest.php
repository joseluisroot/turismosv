<?php
namespace Tests\Feature;
use App\Models\Place;
use App\Models\Review;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
class ReviewTest extends TestCase {
    use RefreshDatabase;
    public function test_a_verified_user_can_publish_and_update_one_review_per_place(): void {
        $this->seed(); $user = User::factory()->create(); $place = Place::where('slug','el-tunco')->firstOrFail();
        $payload = ['rating'=>5,'title'=>'Una tarde memorable','body'=>'El ambiente fue agradable y encontré información útil para disfrutar la visita.','visited_at'=>now()->subDay()->toDateString(),'community_rules'=>'1'];
        $this->actingAs($user)->post(route('reviews.store',$place),$payload)->assertRedirect(route('places.show',$place));
        $this->assertDatabaseHas('reviews',['user_id'=>$user->id,'place_id'=>$place->id,'rating'=>5,'status'=>'published']);
        $this->assertSame(1, Review::count()); $this->assertSame(1, $place->fresh()->reviews_count); $this->assertSame('5.0', $place->fresh()->rating_average);
        $payload['rating']=4; $payload['title']='Actualicé mi experiencia';
        $this->post(route('reviews.store',$place),$payload)->assertRedirect();
        $this->assertSame(1, Review::count()); $this->assertSame(4, Review::first()->rating);
    }
    public function test_an_unverified_user_cannot_publish_a_review(): void {
        $this->seed(); $user=User::factory()->unverified()->create(); $place=Place::first();
        $this->actingAs($user)->post(route('reviews.store',$place),[])->assertRedirect(route('verification.notice'));
        $this->assertDatabaseMissing('reviews',['user_id'=>$user->id,'place_id'=>$place->id]);
    }
    public function test_review_content_and_honesty_confirmation_are_required(): void {
        $this->seed(); $user=User::factory()->create(); $place=Place::first();
        $this->actingAs($user)->post(route('reviews.store',$place),['rating'=>5,'title'=>'Bien','body'=>'Muy corto'])->assertSessionHasErrors(['body','community_rules']);
    }
}
