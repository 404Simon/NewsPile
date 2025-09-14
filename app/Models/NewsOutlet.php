<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class NewsOutlet extends Model
{
    protected $fillable = [
        'name',
        'url',
        'rss_url',
        'b64_logo',
    ];

    public function genres()
    {
        return $this->belongsToMany(Genre::class, 'news_outlet_genre');
    }
}
