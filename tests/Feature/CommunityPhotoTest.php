<?php
namespace Tests\Feature;
use App\Models\Place;
use App\Models\PlacePhoto;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;
class CommunityPhotoTest extends TestCase {
    use RefreshDatabase;
    public function test_verified_user_can_submit_owned_photo_for_private_moderation(): void {
        Storage::fake('local');$this->seed();$user=User::factory()->create();$place=Place::first();$file=UploadedFile::fake()->image('mi-visita.jpg',1200,800)->size(900);
        $this->actingAs($user)->post(route('photos.store',$place),['photo'=>$file,'alt_text'=>'Vista del lugar al atardecer','photo_rights'=>'1','photo_license'=>'1'])->assertRedirect();
        $photo=PlacePhoto::first();$this->assertSame('pending',$photo->status);$this->assertSame('community-photo-2026-08',$photo->license_version);Storage::disk('local')->assertExists($photo->storage_path);$this->get(route('photos.show',$photo->public_id))->assertNotFound();
    }
    public function test_photo_requires_rights_license_and_valid_safe_image(): void {
        Storage::fake('local');$this->seed();$user=User::factory()->create();$place=Place::first();
        $this->actingAs($user)->post(route('photos.store',$place),['photo'=>UploadedFile::fake()->create('archivo.pdf',100,'application/pdf')])->assertSessionHasErrors(['photo','photo_rights','photo_license']);$this->assertDatabaseCount('place_photos',0);
    }
    public function test_only_approved_existing_photo_is_public_and_rendered_on_place(): void {
        Storage::fake('local');$this->seed();$user=User::factory()->create();$place=Place::first();$file=UploadedFile::fake()->image('vista.jpg',1200,800);$path=$file->storeAs("community-photos/{$place->id}",'approved.jpg','local');
        $photo=PlacePhoto::create(['public_id'=>(string)\Illuminate\Support\Str::uuid(),'place_id'=>$place->id,'user_id'=>$user->id,'storage_path'=>$path,'original_name'=>'vista.jpg','mime_type'=>'image/jpeg','file_size'=>$file->getSize(),'alt_text'=>'Vista aprobada','status'=>'approved','license_version'=>'community-photo-2026-08','rights_confirmed_at'=>now(),'moderated_at'=>now()]);
        $this->get(route('photos.show',$photo->public_id))->assertOk()->assertHeader('content-type','image/jpeg');$this->get(route('places.show',$place))->assertOk()->assertSee('Vista aprobada')->assertSee('Uso autorizado');
    }
    public function test_unverified_user_cannot_upload_photo(): void {
        Storage::fake('local');$this->seed();$user=User::factory()->unverified()->create();$place=Place::first();$this->actingAs($user)->post(route('photos.store',$place),[])->assertRedirect(route('verification.notice'));
    }
}
