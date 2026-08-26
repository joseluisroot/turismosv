<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
return new class extends Migration {
    public function up(): void {
        Schema::create('place_qr_codes',function(Blueprint $table){$table->id();$table->foreignId('place_id')->constrained()->cascadeOnDelete();$table->uuid('public_id')->unique();$table->string('token_hash',64);$table->string('label')->default('Código físico principal');$table->boolean('is_active')->default(true)->index();$table->timestamp('expires_at')->nullable()->index();$table->unsignedInteger('successful_scans')->default(0);$table->timestamps();});
        Schema::table('check_ins',function(Blueprint $table){$table->foreignId('place_qr_code_id')->nullable()->after('evidence_method')->constrained()->nullOnDelete();});
    }
    public function down(): void { Schema::table('check_ins',fn(Blueprint $table)=>$table->dropConstrainedForeignId('place_qr_code_id')); Schema::dropIfExists('place_qr_codes'); }
};
