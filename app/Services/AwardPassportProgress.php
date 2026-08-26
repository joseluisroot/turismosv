<?php
namespace App\Services;
use App\Models\Achievement;
use App\Models\PassportStamp;
use App\Models\PointTransaction;
use App\Models\User;
class AwardPassportProgress {
    public function handle(PassportStamp $stamp): void {
        $user=User::query()->lockForUpdate()->findOrFail($stamp->user_id);
        $this->credit($user,"stamp:{$stamp->id}",'passport_stamp',$stamp->id,100,'Visita verificada');
        $stamps=PassportStamp::query()->where('user_id',$user->id)->with('place')->get();
        $progress=['verified_places'=>$stamps->pluck('place_id')->unique()->count(),'verified_departments'=>$stamps->pluck('place.department_id')->unique()->count()];
        Achievement::query()->where('is_active',true)->each(function(Achievement $achievement) use($user,$stamp,$progress){
            if(($progress[$achievement->criteria_type]??0)<$achievement->threshold)return;
            $result=$user->achievements()->syncWithoutDetaching([$achievement->id=>['passport_stamp_id'=>$stamp->id,'earned_at'=>now()]]);
            if(in_array($achievement->id,$result['attached'],true)&&$achievement->points_reward>0)$this->credit($user,"achievement:{$user->id}:{$achievement->id}",'achievement',$achievement->id,$achievement->points_reward,"Logro: {$achievement->name}");
        });
    }
    private function credit(User $user,string $key,string $sourceType,int $sourceId,int $points,string $description): void {
        $transaction=PointTransaction::query()->firstOrCreate(['idempotency_key'=>$key],['user_id'=>$user->id,'source_type'=>$sourceType,'source_id'=>$sourceId,'points'=>$points,'description'=>$description]);
        if($transaction->wasRecentlyCreated)$user->increment('points_balance',$points);
    }
}
