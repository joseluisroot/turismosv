<?php

namespace Database\Seeders;

use App\Models\Achievement;
use App\Models\Category;
use App\Models\Department;
use App\Models\Interest;
use App\Models\Place;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        collect([
            ['code'=>'primer-sello','name'=>'Explorador inicial','description'=>'Consigue tu primera visita verificada.','icon'=>'✦','criteria_type'=>'verified_places','threshold'=>1,'points_reward'=>50],
            ['code'=>'ruta-en-marcha','name'=>'Ruta en marcha','description'=>'Visita tres lugares diferentes.','icon'=>'➜','criteria_type'=>'verified_places','threshold'=>3,'points_reward'=>100],
            ['code'=>'cruza-departamentos','name'=>'Cruza departamentos','description'=>'Registra visitas en dos departamentos.','icon'=>'◇','criteria_type'=>'verified_departments','threshold'=>2,'points_reward'=>150],
        ])->each(fn(array $data)=>Achievement::query()->updateOrCreate(['code'=>$data['code']],$data+['is_active'=>true]));

        $departments = collect([
            ['name' => 'Ahuachapán', 'slug' => 'ahuachapan'],
            ['name' => 'La Libertad', 'slug' => 'la-libertad'],
            ['name' => 'San Salvador', 'slug' => 'san-salvador'],
            ['name' => 'Santa Ana', 'slug' => 'santa-ana'],
            ['name' => 'Sonsonate', 'slug' => 'sonsonate'],
        ])->mapWithKeys(fn (array $data) => [$data['slug'] => Department::query()->updateOrCreate(['slug' => $data['slug']], $data)]);

        $categories = collect([
            ['name' => 'Playas', 'slug' => 'playas', 'icon' => '≈', 'description' => 'Costa, surf y atardeceres frente al Pacífico.'],
            ['name' => 'Montaña', 'slug' => 'montana', 'icon' => '△', 'description' => 'Cumbres, senderos y paisajes de altura.'],
            ['name' => 'Pueblos', 'slug' => 'pueblos', 'icon' => '◇', 'description' => 'Historia, cultura y tradiciones vivas.'],
            ['name' => 'Gastronomía', 'slug' => 'gastronomia', 'icon' => '○', 'description' => 'Sabores locales y experiencias para compartir.'],
        ])->mapWithKeys(fn (array $data) => [$data['slug'] => Category::query()->updateOrCreate(['slug' => $data['slug']], $data)]);

        collect([
            ['name'=>'Playas y surf','slug'=>'playas-surf','icon'=>'≈','description'=>'Olas, arena y atardeceres frente al Pacífico.','category'=>'playas'],['name'=>'Montañas y senderismo','slug'=>'montanas-senderismo','icon'=>'△','description'=>'Cumbres, volcanes, caminos y aire libre.','category'=>'montana'],['name'=>'Pueblos y cultura','slug'=>'pueblos-cultura','icon'=>'◇','description'=>'Tradiciones, artesanía, historia y comunidades.','category'=>'pueblos'],['name'=>'Gastronomía','slug'=>'gastronomia-local','icon'=>'○','description'=>'Sabores salvadoreños y experiencias culinarias.','category'=>'gastronomia'],['name'=>'Naturaleza','slug'=>'naturaleza','icon'=>'✦','description'=>'Paisajes, reservas y biodiversidad.','category'=>'montana'],['name'=>'Historia y arquitectura','slug'=>'historia-arquitectura','icon'=>'▦','description'=>'Memoria, patrimonio, plazas y edificios.','category'=>'pueblos'],['name'=>'Turismo familiar','slug'=>'turismo-familiar','icon'=>'♡','description'=>'Planes para disfrutar juntos y con tranquilidad.','category'=>null],['name'=>'Aventura','slug'=>'aventura','icon'=>'➜','description'=>'Experiencias activas y nuevos desafíos.','category'=>null],
        ])->each(fn(array $data,int $index)=>Interest::query()->updateOrCreate(['slug'=>$data['slug']],['category_id'=>$data['category']?$categories[$data['category']]->id:null,'name'=>$data['name'],'icon'=>$data['icon'],'description'=>$data['description'],'sort_order'=>$index+1,'is_active'=>true]));

        $places = [
            ['name' => 'El Tunco', 'slug' => 'el-tunco', 'summary' => 'Una puerta al Pacífico salvadoreño, reconocida por su ambiente costero y cultura de surf.', 'municipality' => 'Tamanique', 'department' => 'la-libertad', 'category' => 'playas', 'status' => 'community_confirmed', 'score' => 76, 'rating' => 4.7, 'reviews' => 128, 'visits' => 342],
            ['name' => 'Volcán de Santa Ana', 'slug' => 'volcan-de-santa-ana', 'summary' => 'Una experiencia de altura para quienes buscan senderos y panoramas memorables.', 'municipality' => 'Santa Ana', 'department' => 'santa-ana', 'category' => 'montana', 'status' => 'verified', 'score' => 92, 'rating' => 4.8, 'reviews' => 96, 'visits' => 214],
            ['name' => 'Concepción de Ataco', 'slug' => 'concepcion-de-ataco', 'summary' => 'Color, café y tradición en uno de los pueblos más queridos de la zona occidental.', 'municipality' => 'Concepción de Ataco', 'department' => 'ahuachapan', 'category' => 'pueblos', 'status' => 'verified', 'score' => 90, 'rating' => 4.8, 'reviews' => 84, 'visits' => 196],
            ['name' => 'Centro Histórico', 'slug' => 'centro-historico-san-salvador', 'summary' => 'Arquitectura, plazas y memoria urbana en el corazón de la capital.', 'municipality' => 'San Salvador', 'department' => 'san-salvador', 'category' => 'pueblos', 'status' => 'community_confirmed', 'score' => 72, 'rating' => 4.5, 'reviews' => 73, 'visits' => 180],
            ['name' => 'Los Cóbanos', 'slug' => 'los-cobanos', 'summary' => 'Costa y naturaleza marina para descubrir otra expresión del litoral salvadoreño.', 'municipality' => 'Acajutla', 'department' => 'sonsonate', 'category' => 'playas', 'status' => 'registered', 'score' => 48, 'rating' => 4.6, 'reviews' => 41, 'visits' => 89],
            ['name' => 'Ruta del Café de Occidente', 'slug' => 'ruta-del-cafe-occidente', 'summary' => 'Una colección inicial para conectar fincas, sabores y comunidades de tradición cafetalera.', 'municipality' => 'Región occidental', 'department' => 'ahuachapan', 'category' => 'gastronomia', 'status' => 'registered', 'score' => 42, 'rating' => 4.4, 'reviews' => 22, 'visits' => 54],
        ];

        foreach ($places as $place) {
            Place::query()->updateOrCreate(['slug' => $place['slug']], [
                'category_id' => $categories[$place['category']]->id,
                'department_id' => $departments[$place['department']]->id,
                'name' => $place['name'], 'slug' => $place['slug'], 'summary' => $place['summary'],
                'municipality' => $place['municipality'], 'verification_status' => $place['status'],
                'verification_score' => $place['score'], 'rating_average' => $place['rating'],
                'reviews_count' => $place['reviews'], 'verified_visits_count' => $place['visits'],
                'is_featured' => true,
            ]);
        }
    }
}
