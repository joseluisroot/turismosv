<?php
namespace App\Http\Controllers;
use App\Models\PassportStamp;
use App\Models\User;
use Illuminate\Contracts\View\View;
class PublicTravelerProfileController extends Controller {
    public function show(string $publicProfileId): View {
        $traveler=User::query()->where('public_profile_id',$publicProfileId)->where('is_profile_public',true)->firstOrFail();
        $stamps=PassportStamp::query()->whereBelongsTo($traveler)->with(['place.category','place.department'])->latest('earned_at')->get();
        $achievements=$traveler->show_public_achievements?$traveler->achievements()->orderByPivot('earned_at','desc')->get():collect();
        return view('profile.public',['traveler'=>$traveler,'displayName'=>$traveler->public_name_mode==='real'?$traveler->name:$traveler->public_alias,'stamps'=>$traveler->show_public_stamps?$stamps:collect(),'achievements'=>$achievements,'stats'=>['places'=>$stamps->pluck('place_id')->unique()->count(),'departments'=>$stamps->pluck('place.department_id')->unique()->count(),'achievements'=>$traveler->achievements()->count()]]);
    }
}
