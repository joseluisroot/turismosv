<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('departments', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique();
            $table->string('slug')->unique();
            $table->timestamps();
        });

        Schema::create('categories', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->string('icon', 16)->nullable();
            $table->string('description');
            $table->timestamps();
        });

        Schema::create('places', function (Blueprint $table) {
            $table->id();
            $table->foreignId('category_id')->constrained()->cascadeOnUpdate()->restrictOnDelete();
            $table->foreignId('department_id')->constrained()->cascadeOnUpdate()->restrictOnDelete();
            $table->string('name');
            $table->string('slug')->unique();
            $table->text('summary');
            $table->string('municipality');
            $table->enum('verification_status', ['registered', 'community_confirmed', 'verified'])
                ->default('registered')->index();
            $table->unsignedTinyInteger('verification_score')->default(0);
            $table->decimal('rating_average', 2, 1)->nullable();
            $table->unsignedInteger('reviews_count')->default(0);
            $table->unsignedInteger('verified_visits_count')->default(0);
            $table->boolean('is_featured')->default(false)->index();
            $table->timestamps();
            $table->index(['department_id', 'category_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('places');
        Schema::dropIfExists('categories');
        Schema::dropIfExists('departments');
    }
};
