<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Genre extends Model
{
    protected $fillable = ['name'];

    public function articles()
    {
        return $this->belongsToMany(Article::class, 'article_genre');
    }

    public function newsOutlets()
    {
        return $this->belongsToMany(NewsOutlet::class, 'news_outlet_genre');
    }
}
