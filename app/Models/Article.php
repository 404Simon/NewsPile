<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Article extends Model
{
    protected $fillable = [
        'title',
        'content',
        'url',
        'published_at',
    ];

    public function genres()
    {
        return $this->belongsToMany(Genre::class, 'article_genre');
    }
}
