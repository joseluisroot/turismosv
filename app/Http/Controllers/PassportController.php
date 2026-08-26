<?php
namespace App\Http\Controllers;
use App\Models\PassportStamp;
use Illuminate\Contracts\View\View;
class PassportController extends Controller {
    public function show(): View {
        $user=request()->user()->fresh()->load(['achievements'=>fn($query)=>$query->orderByPivot('earned_at','desc')]);$stamps=PassportStamp::query()->whereBelongsTo($user)->with(['place.category','place.department'])->latest('earned_at')->get();
        return view('passport.show',['user'=>$user,'stamps'=>$stamps,'achievements'=>$user->achievements,'stats'=>['places'=>$stamps->pluck('place_id')->unique()->count(),'departments'=>$stamps->pluck('place.department_id')->unique()->count(),'categories'=>$stamps->pluck('place.category_id')->unique()->count()]]);
    }
}
