<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
return new class extends Migration {
    public function up(): void {
        Schema::create('interests',function(Blueprint $table){$table->id();$table->foreignId('category_id')->nullable()->constrained()->nullOnDelete();$table->string('name');$table->string('slug',60)->unique();$table->string('icon',20);$table->string('description');$table->unsignedSmallInteger('sort_order')->default(0);$table->boolean('is_active')->default(true);$table->timestamps();});
        Schema::create('interest_user',function(Blueprint $table){$table->foreignId('interest_id')->constrained()->cascadeOnDelete();$table->foreignId('user_id')->constrained()->cascadeOnDelete();$table->timestamps();$table->primary(['interest_id','user_id']);});
        Schema::table('users',fn(Blueprint $table)=>$table->timestamp('interests_selected_at')->nullable());
        $categories=DB::table('categories')->pluck('id','slug');$now=now();$rows=[
            ['name'=>'Playas y surf','slug'=>'playas-surf','icon'=>'≈','description'=>'Olas, arena y atardeceres frente al Pacífico.','category'=>'playas'],
            ['name'=>'Montañas y senderismo','slug'=>'montanas-senderismo','icon'=>'△','description'=>'Cumbres, volcanes, caminos y aire libre.','category'=>'montana'],
            ['name'=>'Pueblos y cultura','slug'=>'pueblos-cultura','icon'=>'◇','description'=>'Tradiciones, artesanía, historia y comunidades.','category'=>'pueblos'],
            ['name'=>'Gastronomía','slug'=>'gastronomia-local','icon'=>'○','description'=>'Sabores salvadoreños y experiencias culinarias.','category'=>'gastronomia'],
            ['name'=>'Naturaleza','slug'=>'naturaleza','icon'=>'✦','description'=>'Paisajes, reservas y biodiversidad.','category'=>'montana'],
            ['name'=>'Historia y arquitectura','slug'=>'historia-arquitectura','icon'=>'▦','description'=>'Memoria, patrimonio, plazas y edificios.','category'=>'pueblos'],
            ['name'=>'Turismo familiar','slug'=>'turismo-familiar','icon'=>'♡','description'=>'Planes para disfrutar juntos y con tranquilidad.','category'=>null],
            ['name'=>'Aventura','slug'=>'aventura','icon'=>'➜','description'=>'Experiencias activas y nuevos desafíos.','category'=>null],
        ];
        DB::table('interests')->insert(array_map(fn($row)=>['category_id'=>$row['category']?$categories->get($row['category']):null,'name'=>$row['name'],'slug'=>$row['slug'],'icon'=>$row['icon'],'description'=>$row['description'],'sort_order'=>array_search($row,$rows,true)+1,'is_active'=>true,'created_at'=>$now,'updated_at'=>$now],$rows));
    }
    public function down(): void { Schema::table('users',fn(Blueprint $table)=>$table->dropColumn('interests_selected_at'));Schema::dropIfExists('interest_user');Schema::dropIfExists('interests'); }
};
