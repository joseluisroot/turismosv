<?php
namespace App\Http\Controllers;
use App\Models\CheckIn;
use App\Models\PlaceQrCode;
use App\Models\Review;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
class QrCheckInController extends Controller {
    public function show(string $publicId,string $secret): View { $code=$this->resolveCode($publicId,$secret); return view('checkins.qr-confirm',compact('code','secret')); }
    public function confirm(Request $request,string $publicId,string $secret): RedirectResponse {
        $code=$this->resolveCode($publicId,$secret); $user=$request->user();
        DB::transaction(function() use($code,$user){
            $checkIn=CheckIn::query()->where('user_id',$user->id)->where('place_id',$code->place_id)->whereDate('visited_on',today())->lockForUpdate()->first();
            $wasVerified=$checkIn?->status==='verified';
            if($checkIn){$checkIn->update(['status'=>'verified','evidence_method'=>'qr','place_qr_code_id'=>$code->id,'verified_at'=>now(),'verification_note'=>'Validación mediante QR físico activo.']);}
            else{$checkIn=CheckIn::query()->create(['user_id'=>$user->id,'place_id'=>$code->place_id,'visited_on'=>today(),'status'=>'verified','evidence_method'=>'qr','place_qr_code_id'=>$code->id,'verification_consent_at'=>now(),'verified_at'=>now(),'verification_note'=>'Validación mediante QR físico activo.']);}
            if(!$wasVerified){$code->place()->increment('verified_visits_count');$code->increment('successful_scans');}
            Review::query()->where('user_id',$user->id)->where('place_id',$code->place_id)->update(['is_visit_verified'=>true]);
        });
        return redirect()->route('places.show',$code->place)->with('checkin_status','✓ Visita verificada mediante el QR de '.$code->place->name.'.');
    }
    private function resolveCode(string $publicId,string $secret): PlaceQrCode { $code=PlaceQrCode::query()->with('place')->where('public_id',$publicId)->firstOrFail(); abort_unless($code->accepts($secret),404); return $code; }
}
