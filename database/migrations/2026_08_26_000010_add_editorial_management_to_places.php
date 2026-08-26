<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
return new class extends Migration {
    public function up(): void { Schema::table('places',function(Blueprint $table){$table->string('publication_status',20)->default('published')->index();$table->string('source_name')->nullable();$table->string('source_url')->nullable();$table->date('source_verified_at')->nullable();$table->text('editorial_notes')->nullable();$table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();$table->foreignId('editorial_updated_by')->nullable()->constrained('users')->nullOnDelete();$table->timestamp('published_at')->nullable();});DB::table('places')->whereNull('published_at')->update(['published_at'=>now()]); }
    public function down(): void { Schema::table('places',function(Blueprint $table){$table->dropConstrainedForeignId('created_by');$table->dropConstrainedForeignId('editorial_updated_by');$table->dropColumn(['publication_status','source_name','source_url','source_verified_at','editorial_notes','published_at']);}); }
};
