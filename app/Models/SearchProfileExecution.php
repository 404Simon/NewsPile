<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SearchProfileExecution extends Model
{
    use HasFactory;

    protected function casts(): array
    {
        return [
            'executed_at' => 'datetime',
            'articles_checked_until' => 'datetime',
        ];
    }

    protected $fillable = [
        'search_profile_id',
        'executed_at',
        'articles_checked_until',
        'articles_processed',
    ];

    public function searchProfile(): BelongsTo
    {
        return $this->belongsTo(SearchProfile::class);
    }
}
