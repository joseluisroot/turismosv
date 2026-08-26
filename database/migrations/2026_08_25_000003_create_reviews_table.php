<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
return new class extends Migration {
    public function up(): void { Schema::create('reviews', function (Blueprint $table) { $table->id(); $table->foreignId('user_id')->constrained()->cascadeOnDelete(); $table->foreignId('place_id')->constrained()->cascadeOnDelete(); $table->unsignedTinyInteger('rating'); $table->string('title', 120); $table->text('body'); $table->date('visited_at')->nullable(); $table->enum('status', ['published', 'pending', 'rejected'])->default('published')->index(); $table->boolean('is_visit_verified')->default(false)->index(); $table->unsignedInteger('helpful_count')->default(0); $table->timestamps(); $table->unique(['user_id', 'place_id']); $table->index(['place_id', 'status', 'created_at']); }); }
    public function down(): void { Schema::dropIfExists('reviews'); }
};
