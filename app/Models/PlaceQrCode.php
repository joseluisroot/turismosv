<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
class PlaceQrCode extends Model {
    protected $fillable=['place_id','public_id','token_hash','label','is_active','expires_at','successful_scans'];
    protected $hidden=['token_hash'];
    protected function casts(): array { return ['is_active'=>'boolean','expires_at'=>'datetime']; }
    public function place(): BelongsTo { return $this->belongsTo(Place::class); }
    public function accepts(string $secret): bool { return $this->is_active && (!$this->expires_at || $this->expires_at->isFuture()) && hash_equals($this->token_hash,hash_hmac('sha256',$secret,(string)config('app.key'))); }
}
