<?php
namespace Tests\Feature;
use App\Models\Category;
use App\Models\Department;
use App\Models\Place;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
class PlaceAdministrationTest extends TestCase {
    use RefreshDatabase;
    public function test_only_admin_can_manage_catalog(): void {
        $user=User::factory()->create();$this->actingAs($user)->get(route('admin.places.index'))->assertForbidden();$admin=User::factory()->create(['role'=>'admin']);$this->actingAs($admin)->get(route('admin.places.index'))->assertOk()->assertSee('Catálogo de lugares');
    }
    public function test_admin_creates_private_draft_with_unique_slug(): void {
        $this->seed();$admin=User::factory()->create(['role'=>'admin']);$category=Category::first();$department=Department::first();$payload=['name'=>'Nuevo Destino','category_id'=>$category->id,'department_id'=>$department->id,'municipality'=>'Prueba','summary'=>'Descripción editorial suficientemente completa para crear esta nueva ficha turística.','publication_status'=>'draft','verification_status'=>'registered','verification_score'=>10];
        $this->actingAs($admin)->post(route('admin.places.store'),$payload)->assertRedirect();$first=Place::where('name','Nuevo Destino')->first();$this->assertSame('nuevo-destino',$first->slug);$this->post(route('logout'));$this->get(route('places.show',$first))->assertNotFound();$this->actingAs($admin);
        $payload['name']='Nuevo Destino';$this->actingAs($admin)->post(route('admin.places.store'),$payload);$this->assertDatabaseHas('places',['slug'=>'nuevo-destino-2']);
    }
    public function test_publishing_requires_source_and_makes_place_public(): void {
        $this->seed();$admin=User::factory()->create(['role'=>'admin']);$place=Place::first();$place->update(['publication_status'=>'draft','published_at'=>null]);$base=['name'=>$place->name,'category_id'=>$place->category_id,'department_id'=>$place->department_id,'municipality'=>$place->municipality,'summary'=>$place->summary,'publication_status'=>'published','verification_status'=>'verified','verification_score'=>90];
        $this->actingAs($admin)->put(route('admin.places.update',$place),$base)->assertSessionHasErrors(['source_name','source_verified_at']);
        $this->actingAs($admin)->put(route('admin.places.update',$place),$base+['source_name'=>'Institución responsable','source_url'=>'https://example.org/fuente','source_verified_at'=>today()->toDateString(),'editorial_notes'=>'Fuente comprobada por administración.'])->assertSessionHasNoErrors();$place->refresh();$this->assertSame('published',$place->publication_status);$this->assertNotNull($place->published_at);$this->assertSame($admin->id,$place->editorial_updated_by);$this->get(route('places.show',$place))->assertOk();
    }
    public function test_admin_can_create_and_rename_taxonomies(): void {
        $admin=User::factory()->create(['role'=>'admin']);$this->actingAs($admin)->post(route('admin.categories.store'),['name'=>'Museos','icon'=>'▦','description'=>'Arte, memoria y patrimonio cultural.'])->assertRedirect();$category=Category::where('slug','museos')->first();$this->actingAs($admin)->put(route('admin.categories.update',$category),['name'=>'Museos y arte','icon'=>'▦','description'=>'Arte, memoria y patrimonio cultural.'])->assertRedirect();$this->assertSame('museos-y-arte',$category->fresh()->slug);
        $this->actingAs($admin)->post(route('admin.departments.store'),['name'=>'Morazán'])->assertRedirect();$department=Department::where('slug','morazan')->first();$this->actingAs($admin)->put(route('admin.departments.update',$department),['name'=>'Morazán Norte'])->assertRedirect();$this->assertSame('morazan-norte',$department->fresh()->slug);
    }
}
