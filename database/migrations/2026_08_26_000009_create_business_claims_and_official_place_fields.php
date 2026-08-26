<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
return new class extends Migration {
    public function up(): void {
        Schema::create('business_claims',function(Blueprint $table){$table->id();$table->uuid('public_id')->unique();$table->foreignId('place_id')->constrained()->cascadeOnDelete();$table->foreignId('user_id')->constrained()->cascadeOnDelete();$table->string('relationship_role',80);$table->string('business_email');$table->string('business_phone',30);$table->string('evidence_note',500);$table->string('document_path');$table->string('document_name');$table->string('document_mime',60);$table->string('status',20)->default('pending');$table->timestamp('declaration_accepted_at');$table->foreignId('reviewed_by')->nullable()->constrained('users')->nullOnDelete();$table->timestamp('reviewed_at')->nullable();$table->string('review_note',500)->nullable();$table->timestamps();$table->unique(['place_id','user_id']);$table->index(['status','created_at']);});
        Schema::table('places',function(Blueprint $table){$table->string('official_phone',30)->nullable();$table->string('official_whatsapp',30)->nullable();$table->string('official_website')->nullable();$table->string('official_address')->nullable();$table->text('official_opening_hours')->nullable();$table->string('official_price_reference',120)->nullable();$table->text('official_description')->nullable();$table->timestamp('official_updated_at')->nullable();$table->foreignId('official_updated_by')->nullable()->constrained('users')->nullOnDelete();});
    }
    public function down(): void { Schema::table('places',function(Blueprint $table){$table->dropConstrainedForeignId('official_updated_by');$table->dropColumn(['official_phone','official_whatsapp','official_website','official_address','official_opening_hours','official_price_reference','official_description','official_updated_at']);});Schema::dropIfExists('business_claims'); }
};
