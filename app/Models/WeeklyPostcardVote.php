<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
class WeeklyPostcardVote extends Model {
    protected $fillable=['entry_id','user_id','week_start'];
    protected function casts(): array { return ['week_start'=>'date']; }
    public function entry(): BelongsTo { return $this->belongsTo(WeeklyPostcardEntry::class,'entry_id'); }
}
