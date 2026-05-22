<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Source extends Model
{
    //
    protected $fillable = ['name', 'base_url', 'scraper_class', 'is_active'];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function articles()
    {
        return $this->hasMany(Article::class);
    }

    public function scrapingJobs()
    {
        return $this->hasMany(ScrapingJob::class);
    }
}
