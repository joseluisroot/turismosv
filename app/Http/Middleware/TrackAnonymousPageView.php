<?php
namespace App\Http\Middleware;
use Closure;use Illuminate\Http\Request;use Illuminate\Support\Facades\DB;use Symfony\Component\HttpFoundation\Response;
class TrackAnonymousPageView {
 private const TRACKED=['home','explore','rankings.index','postcards.index','places.show','travelers.public','legal.terms','legal.privacy','legal.cookies','legal.community','legal.notice'];
 public function handle(Request $request,Closure $next): Response{return $next($request);}
 public function terminate(Request $request,Response $response): void { $name=$request->route()?->getName();if(!$request->isMethod('GET')||!in_array($name,self::TRACKED,true)||$response->getStatusCode()!==200)return;$parameter=collect($request->route()->parameters())->first();$routeKey=is_object($parameter)&&method_exists($parameter,'getRouteKey')?$parameter->getRouteKey():(string)($parameter??'unknown');$key=in_array($name,['places.show','travelers.public'],true)?$name.':'.$routeKey:$name;try{DB::table('daily_page_metrics')->insertOrIgnore(['metric_date'=>today()->toDateString(),'page_key'=>$key,'views'=>0,'created_at'=>now(),'updated_at'=>now()]);DB::table('daily_page_metrics')->where('metric_date',today()->toDateString())->where('page_key',$key)->increment('views',1,['updated_at'=>now()]);}catch(\Throwable $e){report($e);} }
}
