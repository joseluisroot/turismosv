<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
return new class extends Migration {
    public function up(): void { Schema::create('check_ins', function (Blueprint $table) { $table->id(); $table->foreignId('user_id')->constrained()->cascadeOnDelete(); $table->foreignId('place_id')->constrained()->cascadeOnDelete(); $table->date('visited_on'); $table->enum('status',['pending','verified','rejected'])->default('pending')->index(); $table->enum('evidence_method',['self_reported','geolocation','qr','manual'])->default('self_reported'); $table->string('note',500)->nullable(); $table->timestamp('verification_consent_at'); $table->timestamp('verified_at')->nullable(); $table->foreignId('verified_by')->nullable()->constrained('users')->nullOnDelete(); $table->string('verification_note',500)->nullable(); $table->timestamps(); $table->unique(['user_id','place_id','visited_on']); $table->index(['place_id','status','visited_on']); }); }
    public function down(): void { Schema::dropIfExists('check_ins'); }
};
