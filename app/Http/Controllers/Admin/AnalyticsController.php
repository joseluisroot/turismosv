<?php
namespace App\Http\Controllers\Admin;
use App\Http\Controllers\Controller;use Illuminate\Contracts\View\View;use Illuminate\Support\Facades\DB;
class AnalyticsController extends Controller { public function __invoke(): View { $since=today()->subDays(29);$daily=DB::table('daily_page_metrics')->selectRaw('metric_date, SUM(views) as views')->whereDate('metric_date','>=',$since)->groupBy('metric_date')->orderBy('metric_date')->get();$pages=DB::table('daily_page_metrics')->selectRaw('page_key, SUM(views) as views')->whereDate('metric_date','>=',$since)->groupBy('page_key')->orderByDesc('views')->limit(20)->get();return view('admin.analytics',compact('daily','pages','since')); } }
