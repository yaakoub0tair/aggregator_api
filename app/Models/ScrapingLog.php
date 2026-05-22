<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ScrapingLog extends Model
{
    //
    protected $fillable = [
        'scraping_job_id', 'articles_found',
        'articles_saved', 'error_message'
    ];

    public function job()
    {
        return $this->belongsTo(ScrapingJob::class, 'scraping_job_id');
    }
}
