<?php
namespace App\Services;
use App\Models\CheckIn;
use App\Models\PassportStamp;
use App\Models\Review;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
class VerifyCheckIn {
    public function handle(CheckIn $checkIn,string $method,?int $qrCodeId=null,?int $verifiedBy=null,?string $note=null): PassportStamp {
        return DB::transaction(function() use($checkIn,$method,$qrCodeId,$verifiedBy,$note){
            $checkIn=CheckIn::query()->lockForUpdate()->findOrFail($checkIn->id);$wasVerified=$checkIn->status==='verified';
            $checkIn->update(['status'=>'verified','evidence_method'=>$method,'place_qr_code_id'=>$qrCodeId,'verified_at'=>$checkIn->verified_at??now(),'verified_by'=>$verifiedBy,'verification_note'=>$note]);
            if(!$wasVerified){$checkIn->place()->increment('verified_visits_count');}
            Review::query()->where('user_id',$checkIn->user_id)->where('place_id',$checkIn->place_id)->update(['is_visit_verified'=>true]);
            return PassportStamp::query()->firstOrCreate(['check_in_id'=>$checkIn->id],['public_id'=>(string)Str::uuid(),'user_id'=>$checkIn->user_id,'place_id'=>$checkIn->place_id,'stamp_code'=>'SV-'.strtoupper(Str::random(10)),'earned_at'=>$checkIn->verified_at??now()]);
        });
    }
}
