<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
class Achievement extends Model {
    protected $fillable=['code','name','description','icon','criteria_type','threshold','points_reward','is_active'];
    protected function casts(): array { return ['threshold'=>'integer','points_reward'=>'integer','is_active'=>'boolean']; }
    public function users(): BelongsToMany { return $this->belongsToMany(User::class,'user_achievements')->withPivot(['passport_stamp_id','earned_at'])->withTimestamps(); }
    public function getRouteKeyName(): string { return 'code'; }
}
