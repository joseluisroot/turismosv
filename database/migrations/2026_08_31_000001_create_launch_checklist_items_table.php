<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('launch_checklist_items', function (Blueprint $table) {
            $table->id();
            $table->string('key')->unique();
            $table->string('label');
            $table->string('area', 40);
            $table->boolean('is_required')->default(true);
            $table->boolean('is_completed')->default(false);
            $table->text('evidence_notes')->nullable();
            $table->foreignId('completed_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('completed_at')->nullable();
            $table->timestamps();
        });

        $now = now();
        DB::table('launch_checklist_items')->insert(
            collect(config('launch.checklist'))->map(fn (array $item, string $key) => [
                'key' => $key,
                'label' => $item['label'],
                'area' => $item['area'],
                'is_required' => $item['required'],
                'created_at' => $now,
                'updated_at' => $now,
            ])->values()->all(),
        );
    }

    public function down(): void
    {
        Schema::dropIfExists('launch_checklist_items');
    }
};
