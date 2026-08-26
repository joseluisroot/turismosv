<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;
class CheckIn extends Model {
    protected $fillable=['user_id','place_id','visited_on','status','evidence_method','place_qr_code_id','note','verification_consent_at','verified_at','verified_by','verification_note'];
    protected function casts(): array { return ['visited_on'=>'date','verification_consent_at'=>'datetime','verified_at'=>'datetime']; }
    public function user(): BelongsTo { return $this->belongsTo(User::class); }
    public function place(): BelongsTo { return $this->belongsTo(Place::class); }
    public function verifier(): BelongsTo { return $this->belongsTo(User::class,'verified_by'); }
    public function stamp(): HasOne { return $this->hasOne(PassportStamp::class); }
}
