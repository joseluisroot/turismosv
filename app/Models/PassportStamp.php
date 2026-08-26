<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
class PassportStamp extends Model {
    protected $fillable=['public_id','user_id','place_id','check_in_id','stamp_code','earned_at','is_public'];
    protected function casts(): array { return ['earned_at'=>'datetime','is_public'=>'boolean']; }
    public function user(): BelongsTo { return $this->belongsTo(User::class); }
    public function place(): BelongsTo { return $this->belongsTo(Place::class); }
    public function checkIn(): BelongsTo { return $this->belongsTo(CheckIn::class); }
}
