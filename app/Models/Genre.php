<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Genre extends Model
{
    use HasFactory;

    protected $fillable = ['name'];

    protected $casts = [
        'synonyms' => 'array',
    ];

    public function articles(): BelongsToMany
    {
        return $this->belongsToMany(Article::class)
            ->withTimestamps();
    }

    public function newsOutlets(): BelongsToMany
    {
        return $this->belongsToMany(NewsOutlet::class, 'news_outlet_genre')
            ->withTimestamps();
    }

    public function searchProfiles(): BelongsToMany
    {
        return $this->belongsToMany(SearchProfile::class)
            ->withTimestamps();
    }
}
