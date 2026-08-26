<?php
namespace Tests\Feature;
use App\Models\Place;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
class ExploreSearchTest extends TestCase {
    use RefreshDatabase;
    public function test_search_matches_name_municipality_and_department(): void {
        $this->seed();$this->get(route('explore',['q'=>'El Tunco']))->assertOk()->assertSee('El Tunco')->assertSee('1 resultado');$this->get(route('explore',['q'=>'Acajutla']))->assertOk()->assertSee('Los Cóbanos');$this->get(route('explore',['q'=>'Santa Ana']))->assertOk()->assertSee('Volcán de Santa Ana');
    }
    public function test_combined_filters_return_only_matching_published_places(): void {
        $this->seed();$hidden=Place::where('slug','los-cobanos')->first();$hidden->update(['publication_status'=>'draft']);
        $this->get(route('explore',['category'=>'playas','department'=>'sonsonate']))->assertOk()->assertDontSee('Los Cóbanos')->assertSee('No encontramos coincidencias');
        $this->get(route('explore',['category'=>'pueblos','verification'=>'verified','min_rating'=>4.5]))->assertOk()->assertSee('Concepción de Ataco')->assertDontSee('Centro Histórico');
    }
    public function test_popularity_sort_and_empty_state_are_stable(): void {
        $this->seed();$response=$this->get(route('explore',['sort'=>'popular']))->assertOk();$content=$response->getContent();$this->assertLessThan(strpos($content,'Volcán de Santa Ana'),strpos($content,'El Tunco'));
        $this->get(route('explore',['q'=>'Destino que no existe']))->assertOk()->assertSee('No encontramos coincidencias');
    }
    public function test_invalid_filters_are_rejected_and_home_uses_real_search_route(): void {
        $this->seed();$this->get(route('explore',['sort'=>'inventado']))->assertSessionHasErrors('sort');$this->get(route('home'))->assertOk()->assertSee('action="'.route('explore').'"',false);
    }
}
