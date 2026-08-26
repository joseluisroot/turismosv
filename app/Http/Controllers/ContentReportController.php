<?php
namespace App\Http\Controllers;
use App\Models\ContentReport;
use App\Models\PlacePhoto;
use App\Models\Review;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
class ContentReportController extends Controller {
    public function review(Request $request,Review $review): RedirectResponse { abort_unless($review->status==='published',404);return $this->store($request,$review); }
    public function photo(Request $request,PlacePhoto $photo): RedirectResponse { abort_unless($photo->status==='approved',404);return $this->store($request,$photo); }
    private function store(Request $request,Model $content): RedirectResponse {
        if((int)$content->user_id===(int)$request->user()->id)return back()->withErrors(['report'=>'No necesitas denunciar tu propio contenido; podrás solicitar su edición o retiro.']);
        $validated=$request->validate(['reason'=>['required',Rule::in(['false_information','inappropriate','copyright','privacy','spam','other'])],'details'=>['nullable','string','max:500']]);
        ContentReport::query()->updateOrCreate(['reporter_id'=>$request->user()->id,'reportable_type'=>$content->getMorphClass(),'reportable_id'=>$content->id],$validated+['status'=>'pending','reviewed_by'=>null,'reviewed_at'=>null,'resolution_note'=>null]);
        return back()->with('report_status','Gracias. El contenido fue enviado a revisión.');
    }
}
