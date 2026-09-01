<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LaunchChecklistItem extends Model
{
    protected $fillable = [
        'key',
        'label',
        'area',
        'is_required',
        'is_completed',
        'evidence_notes',
        'completed_by',
        'completed_at',
    ];

    protected function casts(): array
    {
        return [
            'is_required' => 'boolean',
            'is_completed' => 'boolean',
            'completed_at' => 'datetime',
        ];
    }

    public function completedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'completed_by');
    }
}
