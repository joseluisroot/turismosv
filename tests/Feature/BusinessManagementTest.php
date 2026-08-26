<?php
namespace Tests\Feature;
use App\Models\BusinessClaim;
use App\Models\Place;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;
class BusinessManagementTest extends TestCase {
    use RefreshDatabase;
    public function test_verified_user_can_submit_private_business_claim(): void {
        Storage::fake('local');$this->seed();$user=User::factory()->create();$place=Place::first();
        $this->actingAs($user)->post(route('business.claims.store',$place),['relationship_role'=>'Gerente general','business_email'=>'gerencia@negocio.test','business_phone'=>'2222-3333','evidence_note'=>'Soy responsable de la administración y adjunto una constancia vigente.','verification_document'=>UploadedFile::fake()->create('constancia.pdf',400,'application/pdf'),'business_declaration'=>'1'])->assertRedirect();
        $claim=BusinessClaim::first();$this->assertSame('pending',$claim->status);Storage::disk('local')->assertExists($claim->document_path);$this->get(route('admin.moderation.claims.document',$claim))->assertForbidden();
    }
    public function test_admin_approves_claim_and_only_representative_edits_official_fields(): void {
        Storage::fake('local');$this->seed();$admin=User::factory()->create(['role'=>'admin']);$representative=User::factory()->create();$other=User::factory()->create();$place=Place::first();$path=UploadedFile::fake()->create('evidencia.pdf',100,'application/pdf')->storeAs("business-claims/{$place->id}",'claim.pdf','local');
        $claim=BusinessClaim::create(['public_id'=>(string)\Illuminate\Support\Str::uuid(),'place_id'=>$place->id,'user_id'=>$representative->id,'relationship_role'=>'Propietario','business_email'=>'dueno@negocio.test','business_phone'=>'7000-0000','evidence_note'=>'Documento legal que acredita relación con este establecimiento.','document_path'=>$path,'document_name'=>'evidencia.pdf','document_mime'=>'application/pdf','status'=>'pending','declaration_accepted_at'=>now()]);
        $this->actingAs($admin)->get(route('admin.moderation.claims.document',$claim))->assertOk();$this->actingAs($admin)->put(route('admin.moderation.claims.update',$claim),['decision'=>'approved','note'=>'Identidad y documento confirmados'])->assertRedirect();$this->assertSame('approved',$claim->fresh()->status);$this->assertSame($admin->id,$claim->fresh()->reviewed_by);
        $this->actingAs($other)->get(route('merchant.places.edit',$place))->assertForbidden();
        $rating=$place->rating_average;$this->actingAs($representative)->put(route('merchant.places.update',$place),['official_phone'=>'2222-4444','official_website'=>'https://negocio.test','official_description'=>'Información confirmada directamente por el establecimiento.','rating_average'=>1])->assertRedirect();$place->refresh();$this->assertSame('2222-4444',$place->official_phone);$this->assertSame($rating,$place->rating_average);$this->assertSame($representative->id,$place->official_updated_by);
        $this->get(route('places.show',$place))->assertOk()->assertSee('Datos proporcionados por el comercio')->assertSee('Esto no modifica reseñas, estrellas ni ranking');
    }
    public function test_claim_requires_evidence_and_truthful_declaration(): void {
        $this->seed();$user=User::factory()->create();$place=Place::first();$this->actingAs($user)->post(route('business.claims.store',$place),[])->assertSessionHasErrors(['relationship_role','business_email','business_phone','evidence_note','verification_document','business_declaration']);$this->assertDatabaseCount('business_claims',0);
    }
}
