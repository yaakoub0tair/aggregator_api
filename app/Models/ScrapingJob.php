<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ScrapingJob extends Model
{
    //
    protected $fillable = [
        'source_id', 'status',
        'started_at', 'finished_at'
    ];

    protected $casts = [
        'started_at' => 'datetime',
        'finished_at' => 'datetime',
    ];

    public function source()
    {
        return $this->belongsTo(Source::class);
    }

    public function log()
    {
        return $this->hasOne(ScrapingLog::class);
    }
}
