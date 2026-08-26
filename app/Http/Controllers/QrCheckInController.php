<?php
namespace App\Http\Controllers;
use App\Models\CheckIn;
use App\Models\PlaceQrCode;
use App\Services\VerifyCheckIn;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
class QrCheckInController extends Controller {
    public function show(string $publicId,string $secret): View { $code=$this->resolveCode($publicId,$secret); return view('checkins.qr-confirm',compact('code','secret')); }
    public function confirm(Request $request,string $publicId,string $secret,VerifyCheckIn $verifyCheckIn): RedirectResponse {
        $code=$this->resolveCode($publicId,$secret); $user=$request->user();
        DB::transaction(function() use($code,$user,$verifyCheckIn){
            $checkIn=CheckIn::query()->where('user_id',$user->id)->where('place_id',$code->place_id)->whereDate('visited_on',today())->lockForUpdate()->first();
            if(!$checkIn){$checkIn=CheckIn::query()->create(['user_id'=>$user->id,'place_id'=>$code->place_id,'visited_on'=>today(),'status'=>'pending','evidence_method'=>'self_reported','verification_consent_at'=>now()]);}
            $wasVerified=$checkIn->status==='verified';$verifyCheckIn->handle($checkIn,'qr',$code->id,null,'Validación mediante QR físico activo.');if(!$wasVerified){$code->increment('successful_scans');}
        });
        return redirect()->route('passport.show')->with('status','✓ Visita verificada. Agregamos un nuevo sello de '.$code->place->name.' a tu pasaporte.');
    }
    private function resolveCode(string $publicId,string $secret): PlaceQrCode { $code=PlaceQrCode::query()->with('place')->where('public_id',$publicId)->firstOrFail(); abort_unless($code->accepts($secret),404); return $code; }
}
