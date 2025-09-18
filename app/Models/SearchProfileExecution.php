<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

final class SearchProfileExecution extends Model
{
    use HasFactory;

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

    protected function casts(): array
    {
        return [
            'executed_at' => 'datetime',
            'articles_checked_until' => 'datetime',
        ];
    }
}
