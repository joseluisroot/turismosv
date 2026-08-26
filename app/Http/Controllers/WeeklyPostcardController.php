<?php
namespace App\Http\Controllers;
use App\Models\PlacePhoto;
use App\Models\WeeklyPostcardEntry;
use App\Models\WeeklyPostcardVote;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
class WeeklyPostcardController extends Controller {
    public function index(Request $request): View {
        $week=now()->startOfWeek()->toDateString();
        $eligible=fn($query)=>$query->where('status','approved')->whereHas('place',fn($place)=>$place->where('publication_status','published'));
        $entries=WeeklyPostcardEntry::query()->with(['photo.place.department','photo.user'])->withCount('votes')->whereHas('photo',$eligible)->whereDate('week_start',$week)->orderByDesc('votes_count')->orderBy('created_at')->get();
        $winner=WeeklyPostcardEntry::query()->with(['photo.place','photo.user'])->withCount('votes')->whereHas('photo',$eligible)->where('is_winner',true)->latest('week_start')->first();
        $eligiblePhotos=collect();
        if($request->user()?->hasVerifiedEmail())$eligiblePhotos=PlacePhoto::query()->with('place')->whereBelongsTo($request->user())->where('status','approved')->whereHas('place',fn($q)=>$q->where('publication_status','published'))->whereDoesntHave('postcardEntries',fn($q)=>$q->whereDate('week_start',$week))->latest()->get();
        $userVote=$request->user()?WeeklyPostcardVote::where('user_id',$request->user()->id)->whereDate('week_start',$week)->first():null;
        return view('postcards.index',compact('entries','winner','eligiblePhotos','userVote','week'));
    }
    public function nominate(Request $request,PlacePhoto $photo): RedirectResponse {
        abort_unless($photo->user_id===$request->user()->id&&$photo->status==='approved'&&$photo->place->publication_status==='published',403);
        WeeklyPostcardEntry::firstOrCreate(['place_photo_id'=>$photo->id,'week_start'=>now()->startOfWeek()->toDateString()],['user_id'=>$request->user()->id]);
        return back()->with('postcard_status','Tu fotografía participa en la postal de esta semana.');
    }
    public function vote(Request $request,WeeklyPostcardEntry $entry): RedirectResponse {
        $week=now()->startOfWeek()->toDateString();abort_unless($entry->week_start->toDateString()===$week&&$entry->photo->status==='approved'&&$entry->photo->place->publication_status==='published',422);
        if($entry->user_id===$request->user()->id)return back()->withErrors(['vote'=>'No puedes votar por tu propia fotografía.']);
        if(WeeklyPostcardVote::where('user_id',$request->user()->id)->whereDate('week_start',$week)->exists())return back()->withErrors(['vote'=>'Ya utilizaste tu voto de esta semana.']);
        WeeklyPostcardVote::create(['entry_id'=>$entry->id,'user_id'=>$request->user()->id,'week_start'=>$week]);
        return back()->with('postcard_status','Tu voto quedó registrado.');
    }
}
