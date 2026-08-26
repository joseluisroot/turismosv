<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
class WeeklyPostcardEntry extends Model {
    protected $fillable=['place_photo_id','user_id','week_start','is_winner','selected_by','selected_at'];
    protected function casts(): array { return ['week_start'=>'date','is_winner'=>'boolean','selected_at'=>'datetime']; }
    public function photo(): BelongsTo { return $this->belongsTo(PlacePhoto::class,'place_photo_id'); }
    public function user(): BelongsTo { return $this->belongsTo(User::class); }
    public function votes(): HasMany { return $this->hasMany(WeeklyPostcardVote::class,'entry_id'); }
}
