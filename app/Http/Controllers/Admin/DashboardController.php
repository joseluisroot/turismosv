<?php
namespace App\Http\Controllers\Admin;
use App\Http\Controllers\Controller;
use App\Models\BusinessClaim;use App\Models\CheckIn;use App\Models\ContentReport;use App\Models\Place;use App\Models\PlacePhoto;use App\Models\Review;use App\Models\User;use App\Models\WeeklyPostcardEntry;
use Illuminate\Contracts\View\View;
class DashboardController extends Controller {
 public function __invoke(): View { $pending=['photos'=>PlacePhoto::where('status','pending')->count(),'checkins'=>CheckIn::where('status','pending')->count(),'reports'=>ContentReport::where('status','pending')->count(),'claims'=>BusinessClaim::where('status','pending')->count()];return view('admin.dashboard',['pending'=>$pending,'metrics'=>['users'=>User::count(),'verified_users'=>User::whereNotNull('email_verified_at')->count(),'published_places'=>Place::where('publication_status','published')->count(),'draft_places'=>Place::where('publication_status','draft')->count(),'reviews'=>Review::where('status','published')->count(),'verified_visits'=>CheckIn::where('status','verified')->count(),'founder_ready'=>Place::where('is_founder_candidate',true)->where('founder_stage','ready')->count(),'weekly_entries'=>WeeklyPostcardEntry::whereDate('week_start',now()->startOfWeek())->count()],'recentPlaces'=>Place::with('department')->latest('updated_at')->limit(5)->get(),'recentClaims'=>BusinessClaim::with(['place','user'])->latest()->limit(5)->get()]); }
}
