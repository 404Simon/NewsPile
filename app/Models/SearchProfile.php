<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

final class SearchProfile extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'name',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function genres(): BelongsToMany
    {
        return $this->belongsToMany(Genre::class, 'search_profile_genre')
            ->withTimestamps();
    }

    public function newsOutlets(): BelongsToMany
    {
        return $this->belongsToMany(NewsOutlet::class, 'search_profile_news_outlet')
            ->withTimestamps();
    }

    public function articles(): BelongsToMany
    {
        return $this->belongsToMany(Article::class, 'search_profile_article')
            ->withPivot('read_at')
            ->withTimestamps();
    }

    public function executions(): HasMany
    {
        return $this->hasMany(SearchProfileExecution::class);
    }

    public function latestExecution(): ?SearchProfileExecution
    {
        /** @var SearchProfileExecution|null */
        return $this->executions()->latest('executed_at')->first();
    }
}
