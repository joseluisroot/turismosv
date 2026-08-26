<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
class BusinessClaim extends Model {
    protected $fillable=['public_id','place_id','user_id','relationship_role','business_email','business_phone','evidence_note','document_path','document_name','document_mime','status','declaration_accepted_at','reviewed_by','reviewed_at','review_note'];
    protected function casts(): array { return ['declaration_accepted_at'=>'datetime','reviewed_at'=>'datetime']; }
    public function place(): BelongsTo { return $this->belongsTo(Place::class); }
    public function user(): BelongsTo { return $this->belongsTo(User::class); }
    public function reviewer(): BelongsTo { return $this->belongsTo(User::class,'reviewed_by'); }
}
