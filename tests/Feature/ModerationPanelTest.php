<?php
namespace Tests\Feature;
use App\Models\CheckIn;
use App\Models\ContentReport;
use App\Models\Place;
use App\Models\PlacePhoto;
use App\Models\Review;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Tests\TestCase;
class ModerationPanelTest extends TestCase {
    use RefreshDatabase;
    public function test_only_verified_administrator_can_open_panel(): void {
        $traveler=User::factory()->create();$this->actingAs($traveler)->get(route('admin.moderation.index'))->assertForbidden();$admin=User::factory()->create(['role'=>'admin']);$this->actingAs($admin)->get(route('admin.moderation.index'))->assertOk()->assertSee('Centro de moderación');
    }
    public function test_admin_can_approve_photo_with_auditable_decision(): void {
        Storage::fake('local');$this->seed();$admin=User::factory()->create(['role'=>'admin']);$author=User::factory()->create();$place=Place::first();$file=UploadedFile::fake()->image('foto.jpg',1200,800);$path=$file->storeAs("community-photos/{$place->id}",'pending.jpg','local');$photo=PlacePhoto::create(['public_id'=>(string)Str::uuid(),'place_id'=>$place->id,'user_id'=>$author->id,'storage_path'=>$path,'original_name'=>'foto.jpg','mime_type'=>'image/jpeg','file_size'=>$file->getSize(),'status'=>'pending','license_version'=>'community-photo-2026-08','rights_confirmed_at'=>now()]);
        $this->actingAs($admin)->put(route('admin.moderation.photos.update',$photo),['decision'=>'approved','note'=>'Autoría y pertinencia revisadas'])->assertRedirect();$photo->refresh();$this->assertSame('approved',$photo->status);$this->assertSame($admin->id,$photo->moderated_by);$this->get(route('photos.show',$photo->public_id))->assertOk();
    }
    public function test_admin_verification_issues_stamp_and_points_once(): void {
        $this->seed();$admin=User::factory()->create(['role'=>'admin']);$traveler=User::factory()->create();$place=Place::first();$checkIn=CheckIn::create(['user_id'=>$traveler->id,'place_id'=>$place->id,'visited_on'=>today(),'status'=>'pending','evidence_method'=>'self_reported','verification_consent_at'=>now()]);
        $this->actingAs($admin)->put(route('admin.moderation.checkins.update',$checkIn),['decision'=>'verified','note'=>'Evidencia revisada'])->assertRedirect();$this->assertSame('verified',$checkIn->fresh()->status);$this->assertSame($admin->id,$checkIn->fresh()->verified_by);$this->assertDatabaseCount('passport_stamps',1);$this->assertSame(150,$traveler->fresh()->points_balance);
    }
    public function test_report_can_remove_published_review_and_records_resolution(): void {
        $this->seed();$admin=User::factory()->create(['role'=>'admin']);$author=User::factory()->create();$reporter=User::factory()->create();$place=Place::first();$review=Review::create(['user_id'=>$author->id,'place_id'=>$place->id,'rating'=>1,'title'=>'Contenido denunciado','body'=>'Texto de prueba suficientemente detallado.','status'=>'published']);
        $this->actingAs($reporter)->post(route('reports.reviews.store',$review),['reason'=>'false_information','details'=>'La información necesita revisión'])->assertRedirect();$report=ContentReport::first();$this->actingAs($admin)->put(route('admin.moderation.reports.update',$report),['decision'=>'removed','note'=>'Se confirmó incumplimiento'])->assertRedirect();$this->assertSame('rejected',$review->fresh()->status);$this->assertSame('removed',$report->fresh()->status);$this->assertSame($admin->id,$report->fresh()->reviewed_by);
    }
}
