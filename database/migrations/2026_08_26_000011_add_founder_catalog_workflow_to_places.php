<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
return new class extends Migration {
    public function up(): void { Schema::table('places',function(Blueprint $table){$table->boolean('is_founder_candidate')->default(false)->index();$table->string('founder_priority',20)->nullable();$table->string('founder_stage',30)->nullable()->index();$table->string('founder_contact_status',30)->nullable();$table->string('founder_photo_status',30)->nullable();$table->string('founder_contact_name')->nullable();$table->string('founder_contact_email')->nullable();$table->timestamp('founder_contacted_at')->nullable();$table->text('founder_notes')->nullable();$table->foreignId('founder_assigned_to')->nullable()->constrained('users')->nullOnDelete();});
        $now=now();foreach(['Ahuachapán'=>'ahuachapan','Cabañas'=>'cabanas','Chalatenango'=>'chalatenango','Cuscatlán'=>'cuscatlan','La Libertad'=>'la-libertad','La Paz'=>'la-paz','La Unión'=>'la-union','Morazán'=>'morazan','San Miguel'=>'san-miguel','San Salvador'=>'san-salvador','San Vicente'=>'san-vicente','Santa Ana'=>'santa-ana','Sonsonate'=>'sonsonate','Usulután'=>'usulutan'] as $name=>$slug)DB::table('departments')->updateOrInsert(['slug'=>$slug],['name'=>$name,'created_at'=>$now,'updated_at'=>$now]);
        DB::table('places')->whereIn('slug',['el-tunco','volcan-de-santa-ana','concepcion-de-ataco','centro-historico-san-salvador','los-cobanos','ruta-del-cafe-occidente'])->update(['is_founder_candidate'=>true,'founder_priority'=>'high','founder_stage'=>'researching','founder_contact_status'=>'not_started','founder_photo_status'=>'missing']);
    }
    public function down(): void { Schema::table('places',function(Blueprint $table){$table->dropConstrainedForeignId('founder_assigned_to');$table->dropColumn(['is_founder_candidate','founder_priority','founder_stage','founder_contact_status','founder_photo_status','founder_contact_name','founder_contact_email','founder_contacted_at','founder_notes']);}); }
};
