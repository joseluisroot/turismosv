<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
class Interest extends Model {
    protected $fillable=['category_id','name','slug','icon','description','sort_order','is_active'];
    protected function casts(): array { return ['is_active'=>'boolean']; }
    public function category(): BelongsTo { return $this->belongsTo(Category::class); }
    public function users(): BelongsToMany { return $this->belongsToMany(User::class)->withTimestamps(); }
}
