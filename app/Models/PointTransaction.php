<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
class PointTransaction extends Model {
    protected $fillable=['user_id','idempotency_key','source_type','source_id','points','description'];
    public function user(): BelongsTo { return $this->belongsTo(User::class); }
}
