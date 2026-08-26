<?php
namespace App\Http\Controllers\Admin;
use App\Http\Controllers\Controller;
use App\Models\CheckIn;
use App\Models\ContentReport;
use App\Models\PlacePhoto;
use App\Models\Review;
use App\Models\BusinessClaim;
use App\Services\VerifyCheckIn;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
class ModerationController extends Controller {
    public function index(): View {
        return view('admin.moderation.index',['photos'=>PlacePhoto::with(['place','user'])->where('status','pending')->oldest()->limit(30)->get(),'checkIns'=>CheckIn::with(['place','user'])->where('status','pending')->oldest()->limit(30)->get(),'reports'=>ContentReport::with(['reporter','reportable'])->where('status','pending')->oldest()->limit(30)->get(),'claims'=>BusinessClaim::with(['place','user'])->where('status','pending')->oldest()->limit(30)->get(),'counts'=>['photos'=>PlacePhoto::where('status','pending')->count(),'checkins'=>CheckIn::where('status','pending')->count(),'reports'=>ContentReport::where('status','pending')->count(),'claims'=>BusinessClaim::where('status','pending')->count()]]);
    }
    public function photo(Request $request,PlacePhoto $photo): RedirectResponse {
        $validated=$request->validate(['decision'=>['required',Rule::in(['approved','rejected'])],'note'=>['nullable','string','max:500']]);abort_unless($photo->status==='pending',409);
        $photo->update(['status'=>$validated['decision'],'moderated_by'=>$request->user()->id,'moderated_at'=>now(),'moderation_note'=>$validated['note']??null]);return back()->with('moderation_status','Fotografía moderada correctamente.');
    }
    public function photoPreview(PlacePhoto $photo): BinaryFileResponse { abort_unless(Storage::disk('local')->exists($photo->storage_path),404);return response()->file(Storage::disk('local')->path($photo->storage_path),['Content-Type'=>$photo->mime_type,'Cache-Control'=>'private, no-store','X-Content-Type-Options'=>'nosniff']); }
    public function checkIn(Request $request,CheckIn $checkIn,VerifyCheckIn $verify): RedirectResponse {
        $validated=$request->validate(['decision'=>['required',Rule::in(['verified','rejected'])],'note'=>[Rule::requiredIf($request->input('decision')==='rejected'),'nullable','string','max:500']]);abort_unless($checkIn->status==='pending',409);
        if($validated['decision']==='verified')$verify->handle($checkIn,'manual',null,$request->user()->id,$validated['note']??'Aprobada desde moderación');else $checkIn->update(['status'=>'rejected','verified_by'=>$request->user()->id,'verification_note'=>$validated['note']]);return back()->with('moderation_status','Visita moderada correctamente.');
    }
    public function report(Request $request,ContentReport $report): RedirectResponse {
        $validated=$request->validate(['decision'=>['required',Rule::in(['dismissed','removed'])],'note'=>['required','string','max:500']]);abort_unless($report->status==='pending',409);
        DB::transaction(function()use($request,$report,$validated){$target=$report->reportable;if($validated['decision']==='removed'&&$target instanceof Review)$target->update(['status'=>'rejected']);if($validated['decision']==='removed'&&$target instanceof PlacePhoto)$target->update(['status'=>'rejected','moderated_by'=>$request->user()->id,'moderated_at'=>now(),'moderation_note'=>$validated['note']]);$report->update(['status'=>$validated['decision'],'reviewed_by'=>$request->user()->id,'reviewed_at'=>now(),'resolution_note'=>$validated['note']]);});
        return back()->with('moderation_status','Denuncia resuelta y decisión registrada.');
    }
    public function claim(Request $request,BusinessClaim $claim): RedirectResponse { $validated=$request->validate(['decision'=>['required',Rule::in(['approved','rejected'])],'note'=>['required','string','max:500']]);abort_unless($claim->status==='pending',409);$claim->update(['status'=>$validated['decision'],'reviewed_by'=>$request->user()->id,'reviewed_at'=>now(),'review_note'=>$validated['note']]);return back()->with('moderation_status','Solicitud comercial revisada.'); }
    public function claimDocument(BusinessClaim $claim): BinaryFileResponse { abort_unless(Storage::disk('local')->exists($claim->document_path),404);return response()->file(Storage::disk('local')->path($claim->document_path),['Content-Type'=>$claim->document_mime,'Cache-Control'=>'private, no-store','X-Content-Type-Options'=>'nosniff']); }
}
