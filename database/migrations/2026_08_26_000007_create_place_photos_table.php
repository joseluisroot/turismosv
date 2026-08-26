<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
return new class extends Migration {
    public function up(): void { Schema::create('place_photos',function(Blueprint $table){$table->id();$table->uuid('public_id')->unique();$table->foreignId('place_id')->constrained()->cascadeOnDelete();$table->foreignId('user_id')->constrained()->cascadeOnDelete();$table->string('storage_path');$table->string('original_name');$table->string('mime_type',50);$table->unsignedBigInteger('file_size');$table->string('alt_text',160)->nullable();$table->string('status',20)->default('pending');$table->string('license_version',30);$table->timestamp('rights_confirmed_at');$table->foreignId('moderated_by')->nullable()->constrained('users')->nullOnDelete();$table->timestamp('moderated_at')->nullable();$table->string('moderation_note',500)->nullable();$table->timestamps();$table->index(['place_id','status']);$table->index(['user_id','status']);}); }
    public function down(): void { Schema::dropIfExists('place_photos'); }
};
