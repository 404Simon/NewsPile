<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

final class NewsOutlet extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'url',
        'rss_url',
        'b64_logo',
    ];

    public function articles(): HasMany
    {
        return $this->hasMany(Article::class);
    }

    public function genres(): BelongsToMany
    {
        return $this->belongsToMany(Genre::class, 'news_outlet_genre')
            ->withTimestamps();
    }

    public function searchProfiles(): BelongsToMany
    {
        return $this->belongsToMany(SearchProfile::class)
            ->withTimestamps();
    }
}
