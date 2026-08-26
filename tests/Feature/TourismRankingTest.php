<?php
namespace Tests\Feature;
use App\Models\Category;
use App\Models\Place;
use App\Services\RankPlaces;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
class TourismRankingTest extends TestCase {
    use RefreshDatabase;
    public function test_general_ranking_uses_transparent_weighted_order(): void {
        $this->seed();$response=$this->get(route('rankings.index'))->assertOk()->assertSee('50%')->assertSee('25%')->assertSee('Ningún patrocinio puede comprar una posición');$content=$response->getContent();$this->assertLessThan(strpos($content,'Concepción de Ataco'),strpos($content,'Volcán de Santa Ana'));
        $ranked=app(RankPlaces::class)->handle(Place::where('publication_status','published')->get());$this->assertSame('Volcán de Santa Ana',$ranked->first()->name);$this->assertSame(1,$ranked->first()->ranking_position);
    }
    public function test_category_ranking_has_clean_url_and_only_matching_places(): void {
        $this->seed();$category=Category::where('slug','playas')->first();$this->assertStringEndsWith('/rankings/playas',route('rankings.index',$category));$this->get(route('rankings.index',$category))->assertOk()->assertSee('El Tunco')->assertSee('Los Cóbanos')->assertDontSee('Volcán de Santa Ana');
    }
    public function test_drafts_and_archived_places_never_enter_rankings(): void {
        $this->seed();$place=Place::where('slug','volcan-de-santa-ana')->first();$place->update(['publication_status'=>'draft']);$this->get(route('rankings.index'))->assertOk()->assertDontSee('Volcán de Santa Ana');$ranked=app(RankPlaces::class)->handle(Place::where('publication_status','published')->get());$this->assertFalse($ranked->contains('id',$place->id));
    }
    public function test_review_volume_adjustment_prevents_single_rating_from_dominating(): void {
        $this->seed();$places=Place::whereIn('slug',['el-tunco','los-cobanos'])->get();$weak=$places->firstWhere('slug','los-cobanos');$strong=$places->firstWhere('slug','el-tunco');$weak->setAttribute('rating_average',5.0);$weak->setAttribute('reviews_count',1);$weak->setAttribute('verification_score',20);$weak->setAttribute('verified_visits_count',1);$ranked=app(RankPlaces::class)->handle(collect([$weak,$strong]));$this->assertSame($strong->id,$ranked->first()->id);
    }
}
