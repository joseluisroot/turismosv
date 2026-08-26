<?php
use Illuminate\Database\Migrations\Migration;use Illuminate\Database\Schema\Blueprint;use Illuminate\Support\Facades\Schema;
return new class extends Migration { public function up(): void { Schema::create('daily_page_metrics',function(Blueprint $table){$table->id();$table->date('metric_date');$table->string('page_key',100);$table->unsignedBigInteger('views')->default(0);$table->timestamps();$table->unique(['metric_date','page_key']);}); }public function down(): void{Schema::dropIfExists('daily_page_metrics');} };
