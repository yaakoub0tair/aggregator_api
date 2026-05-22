<?php

namespace App\Jobs;

use App\Models\Source;
use App\Models\ScrapingJob;
use App\Models\ScrapingLog;
use App\Services\Scrapers\ScraperFactory;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class ScrapeSourceJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(public Source $source) {}

    public function handle(): void
    {
        // Create job record
        $job = ScrapingJob::create([
            'source_id'  => $this->source->id,
            'status'     => 'running',
            'started_at' => now(),
        ]);

        try {
            $scraper = ScraperFactory::make($this->source);
            $result  = $scraper->scrape();

            // Save log
            ScrapingLog::create([
                'scraping_job_id' => $job->id,
                'articles_found'  => $result['found'],
                'articles_saved'  => $result['saved'],
                'error_message'   => !empty($result['errors'])
                    ? implode("\n", $result['errors'])
                    : null,
            ]);

            $job->update([
                'status'      => 'completed',
                'finished_at' => now(),
            ]);

        } catch (\Exception $e) {
            ScrapingLog::create([
                'scraping_job_id' => $job->id,
                'articles_found'  => 0,
                'articles_saved'  => 0,
                'error_message'   => $e->getMessage(),
            ]);

            $job->update([
                'status'      => 'failed',
                'finished_at' => now(),
            ]);
        }
    }
}