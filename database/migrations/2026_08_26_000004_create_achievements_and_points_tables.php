<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
return new class extends Migration {
    public function up(): void {
        Schema::table('users',fn(Blueprint $table)=>$table->unsignedInteger('points_balance')->default(0)->after('terms_version'));
        Schema::create('achievements',function(Blueprint $table){$table->id();$table->string('code',60)->unique();$table->string('name');$table->string('description');$table->string('icon',20)->default('★');$table->string('criteria_type',40);$table->unsignedInteger('threshold');$table->unsignedInteger('points_reward')->default(0);$table->boolean('is_active')->default(true);$table->timestamps();});
        Schema::create('user_achievements',function(Blueprint $table){$table->id();$table->foreignId('user_id')->constrained()->cascadeOnDelete();$table->foreignId('achievement_id')->constrained()->cascadeOnDelete();$table->foreignId('passport_stamp_id')->nullable()->constrained()->nullOnDelete();$table->timestamp('earned_at');$table->timestamps();$table->unique(['user_id','achievement_id']);});
        Schema::create('point_transactions',function(Blueprint $table){$table->id();$table->foreignId('user_id')->constrained()->cascadeOnDelete();$table->string('idempotency_key')->unique();$table->string('source_type',40);$table->unsignedBigInteger('source_id')->nullable();$table->integer('points');$table->string('description');$table->timestamps();$table->index(['user_id','created_at']);});
        $now=now();DB::table('achievements')->insert([
            ['code'=>'primer-sello','name'=>'Explorador inicial','description'=>'Consigue tu primera visita verificada.','icon'=>'✦','criteria_type'=>'verified_places','threshold'=>1,'points_reward'=>50,'is_active'=>true,'created_at'=>$now,'updated_at'=>$now],
            ['code'=>'ruta-en-marcha','name'=>'Ruta en marcha','description'=>'Visita tres lugares diferentes.','icon'=>'➜','criteria_type'=>'verified_places','threshold'=>3,'points_reward'=>100,'is_active'=>true,'created_at'=>$now,'updated_at'=>$now],
            ['code'=>'cruza-departamentos','name'=>'Cruza departamentos','description'=>'Registra visitas en dos departamentos.','icon'=>'◇','criteria_type'=>'verified_departments','threshold'=>2,'points_reward'=>150,'is_active'=>true,'created_at'=>$now,'updated_at'=>$now],
        ]);
    }
    public function down(): void { Schema::dropIfExists('point_transactions');Schema::dropIfExists('user_achievements');Schema::dropIfExists('achievements');Schema::table('users',fn(Blueprint $table)=>$table->dropColumn('points_balance')); }
};
