<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SearchProfile extends Model
{
    protected $fillable = [
        'user_id',
        'name',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function newsOutlets()
    {
        return $this->belongsToMany(NewsOutlet::class, 'search_profile_news_outlet');
    }

    public function articles()
    {
        return $this->belongsToMany(Article::class, 'search_profile_article');
    }
}
