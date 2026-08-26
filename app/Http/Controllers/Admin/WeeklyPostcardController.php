<?php
namespace App\Http\Controllers\Admin;
use App\Http\Controllers\Controller;
use App\Models\WeeklyPostcardEntry;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
class WeeklyPostcardController extends Controller {
    public function select(Request $request): RedirectResponse {
        $data=$request->validate(['week_start'=>['required','date','before:'.now()->startOfWeek()->toDateString()]]);
        $winner=WeeklyPostcardEntry::query()->withCount('votes')->whereHas('photo',fn($photo)=>$photo->where('status','approved')->whereHas('place',fn($place)=>$place->where('publication_status','published')))->whereDate('week_start',$data['week_start'])->orderByDesc('votes_count')->orderBy('created_at')->first();
        if(!$winner)return back()->withErrors(['week_start'=>'Esa semana no tiene fotografías participantes.']);
        DB::transaction(function()use($winner,$data,$request){WeeklyPostcardEntry::whereDate('week_start',$data['week_start'])->update(['is_winner'=>false,'selected_by'=>null,'selected_at'=>null]);$winner->update(['is_winner'=>true,'selected_by'=>$request->user()->id,'selected_at'=>now()]);});
        return back()->with('postcard_status','Postal ganadora seleccionada según el conteo registrado.');
    }
}
