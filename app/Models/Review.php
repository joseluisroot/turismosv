<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
class Review extends Model {
    protected $fillable = ['user_id', 'place_id', 'rating', 'title', 'body', 'visited_at', 'status'];
    protected function casts(): array { return ['visited_at' => 'date', 'is_visit_verified' => 'boolean']; }
    public function user(): BelongsTo { return $this->belongsTo(User::class); }
    public function place(): BelongsTo { return $this->belongsTo(Place::class); }
}
