<?php
namespace App\Http\Controllers;
use App\Models\Place;use Illuminate\Http\Response;
class SeoController extends Controller {
 public function sitemap(): Response { $places=Place::where('publication_status','published')->latest('updated_at')->get(['slug','updated_at']);return response()->view('seo.sitemap',compact('places'))->header('Content-Type','application/xml'); }
 public function robots(): Response { return response("User-agent: *\nAllow: /\nDisallow: /administracion\nDisallow: /mi-perfil\nDisallow: /mi-pasaporte\nDisallow: /mis-comercios\nDisallow: /verificar-correo\nSitemap: ".route('seo.sitemap')."\n",200,['Content-Type'=>'text/plain']); }
}
