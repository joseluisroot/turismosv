<?php
namespace Database\Seeders;
use App\Models\Category;
use App\Models\Department;
use App\Models\Place;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;
class FounderCatalogSeeder extends Seeder {
    public function run(): void {
        Place::query()->whereIn('slug',['el-tunco','volcan-de-santa-ana','concepcion-de-ataco','centro-historico-san-salvador','los-cobanos','ruta-del-cafe-occidente'])->update([
            'is_founder_candidate'=>true,'founder_priority'=>'high','founder_stage'=>'researching','founder_contact_status'=>'not_started','founder_photo_status'=>'missing',
        ]);
        $official='https://www.elsalvador.travel/es/';$nature='https://elsalvador.travel/esp/naturaleza-y-aventura/';$parks='https://www.mitur.gob.sv/preguntas-frecuentes/';$east='https://www.mitur.gob.sv/programa-bid/';
        $items=[
            ['Apaneca','Ahuachapán','Apaneca','pueblos',$official],['Laguna Verde','Ahuachapán','Apaneca','montana',$nature],
            ['Ilobasco','Cabañas','Ilobasco','pueblos',$official],['Parque Ecológico Bosque de Cinquera','Cabañas','Cinquera','montana',$official],
            ['La Palma','Chalatenango','La Palma','pueblos',$official],['Cerro El Pital','Chalatenango','San Ignacio','montana',$nature],['La Montañona','Chalatenango','Chalatenango','montana',$official],
            ['Suchitoto','Cuscatlán','Suchitoto','pueblos',$official],['Lago de Suchitlán','Cuscatlán','Suchitoto','montana',$nature],
            ['Playa El Zonte','La Libertad','Chiltiupán','playas',$official],['Parque Walter Thilo Deininger','La Libertad','La Libertad','montana',$parks],
            ['Costa del Sol','La Paz','San Luis La Herradura','playas',$parks],['Parque Recreativo Ichanmichen','La Paz','Zacatecoluca','montana',$parks],
            ['Volcán de Conchagua','La Unión','Conchagua','montana',$east],['Golfo de Fonseca','La Unión','La Unión','playas',$official],['Playa Las Tunas','La Unión','Conchagua','playas',$official],
            ['Perquín','Morazán','Perquín','pueblos',$east],['El Mozote','Morazán','Meanguera','pueblos',$official],['Río Sapo','Morazán','Arambala','montana',$nature],
            ['Playa El Cuco','San Miguel','Chirilagua','playas',$official],['Volcán Chaparrastique','San Miguel','San Miguel','montana',$nature],['Laguna de Olomega','San Miguel','Chirilagua','montana',$nature],
            ['Puerta del Diablo','San Salvador','Panchimalco','montana',$official],['Parque Balboa','San Salvador','Panchimalco','montana',$parks],
            ['Laguna de Apastepeque','San Vicente','Apastepeque','montana',$parks],['Parque Recreativo Amapulapa','San Vicente','San Vicente','montana',$parks],['Volcán Chichontepec','San Vicente','San Vicente','montana',$nature],
            ['Lago de Coatepeque','Santa Ana','El Congo','montana',$nature],['Parque Recreativo Cerro Verde','Santa Ana','El Congo','montana',$nature],['Parque Arqueológico Tazumal','Santa Ana','Chalchuapa','pueblos',$official],
            ['Juayúa','Sonsonate','Juayúa','pueblos',$official],['Parque Recreativo Atecozol','Sonsonate','Izalco','montana',$parks],
            ['Bahía de Jiquilisco','Usulután','Jiquilisco','playas',$nature],['Laguna de Alegría','Usulután','Alegría','montana',$east],['Puerto El Triunfo','Usulután','Puerto El Triunfo','pueblos',$official],
        ];
        foreach($items as [$name,$departmentName,$municipality,$categorySlug,$source]){$department=Department::where('name',$departmentName)->firstOrFail();$category=Category::where('slug',$categorySlug)->firstOrFail();$slug=Str::slug($name);Place::updateOrCreate(['slug'=>$slug],['category_id'=>$category->id,'department_id'=>$department->id,'name'=>$name,'summary'=>"Candidato del catálogo fundador en {$departmentName}. Su información detallada, acceso y condiciones de visita están pendientes de verificación editorial antes de publicarse.",'municipality'=>$municipality,'verification_status'=>'registered','verification_score'=>0,'rating_average'=>null,'reviews_count'=>0,'verified_visits_count'=>0,'is_featured'=>false,'publication_status'=>'draft','source_name'=>'Fuente turística oficial pendiente de validación específica','source_url'=>$source,'source_verified_at'=>null,'editorial_notes'=>'Candidato inicial. No publicar hasta confirmar datos específicos, contacto y derechos de fotografías.','is_founder_candidate'=>true,'founder_priority'=>'medium','founder_stage'=>'candidate','founder_contact_status'=>'not_started','founder_photo_status'=>'missing']);}
    }
}
