<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
return new class extends Migration {
    public function up(): void { Schema::table('users',function(Blueprint $table){$table->uuid('public_profile_id')->nullable()->unique()->after('points_balance');$table->boolean('is_profile_public')->default(false);$table->string('public_name_mode',10)->default('alias');$table->string('public_alias',40)->nullable();$table->boolean('show_public_achievements')->default(true);$table->boolean('show_public_stamps')->default(false);$table->timestamp('public_profile_updated_at')->nullable();}); }
    public function down(): void { Schema::table('users',function(Blueprint $table){$table->dropUnique(['public_profile_id']);$table->dropColumn(['public_profile_id','is_profile_public','public_name_mode','public_alias','show_public_achievements','show_public_stamps','public_profile_updated_at']);}); }
};
