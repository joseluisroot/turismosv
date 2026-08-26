<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Place extends Model
{
    use HasFactory;

    protected $fillable = [
        'category_id', 'department_id', 'name', 'slug', 'summary', 'municipality',
        'verification_status', 'verification_score', 'rating_average', 'reviews_count',
        'verified_visits_count', 'is_featured',
        'official_phone','official_whatsapp','official_website','official_address','official_opening_hours','official_price_reference','official_description','official_updated_at','official_updated_by',
        'publication_status','source_name','source_url','source_verified_at','editorial_notes','created_by','editorial_updated_by','published_at',
    ];

    protected function casts(): array
    {
        return ['rating_average' => 'decimal:1', 'is_featured' => 'boolean','official_updated_at'=>'datetime','source_verified_at'=>'date','published_at'=>'datetime'];
    }

    public function getRouteKeyName(): string
    {
        return 'slug';
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function department(): BelongsTo
    {
        return $this->belongsTo(Department::class);
    }

    public function reviews(): HasMany
    {
        return $this->hasMany(Review::class);
    }

    public function checkIns(): HasMany
    {
        return $this->hasMany(CheckIn::class);
    }

    public function photos(): HasMany { return $this->hasMany(PlacePhoto::class); }
    public function businessClaims(): HasMany { return $this->hasMany(BusinessClaim::class); }
    public function officialEditor(): BelongsTo { return $this->belongsTo(User::class,'official_updated_by'); }
}
