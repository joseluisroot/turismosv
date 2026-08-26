<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
class PlacePhoto extends Model {
    protected $fillable=['public_id','place_id','user_id','storage_path','original_name','mime_type','file_size','alt_text','status','license_version','rights_confirmed_at','moderated_by','moderated_at','moderation_note'];
    protected function casts(): array { return ['rights_confirmed_at'=>'datetime','moderated_at'=>'datetime','file_size'=>'integer']; }
    public function place(): BelongsTo { return $this->belongsTo(Place::class); }
    public function user(): BelongsTo { return $this->belongsTo(User::class); }
    public function moderator(): BelongsTo { return $this->belongsTo(User::class,'moderated_by'); }
    public function postcardEntries(): HasMany { return $this->hasMany(WeeklyPostcardEntry::class); }
}
