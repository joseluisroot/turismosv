<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
return new class extends Migration {
    public function up(): void { Schema::create('passport_stamps',function(Blueprint $table){$table->id();$table->uuid('public_id')->unique();$table->foreignId('user_id')->constrained()->cascadeOnDelete();$table->foreignId('place_id')->constrained()->cascadeOnDelete();$table->foreignId('check_in_id')->unique()->constrained('check_ins')->cascadeOnDelete();$table->string('stamp_code',40)->unique();$table->timestamp('earned_at');$table->boolean('is_public')->default(false);$table->timestamps();$table->index(['user_id','earned_at']);}); }
    public function down(): void { Schema::dropIfExists('passport_stamps'); }
};
