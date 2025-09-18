<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Article extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'description',
        'content',
        'url',
        'published_at',
        'news_outlet_id',
    ];

    protected $casts = [
        'published_at' => 'date',
    ];

    public function newsOutlet(): BelongsTo
    {
        return $this->belongsTo(NewsOutlet::class);
    }

    public function genres(): BelongsToMany
    {
        return $this->belongsToMany(Genre::class)
            ->withTimestamps();
    }
}
