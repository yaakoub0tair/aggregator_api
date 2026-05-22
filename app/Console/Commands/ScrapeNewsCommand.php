<?php

namespace App\Console\Commands;

use App\Jobs\ScrapeSourceJob;
use App\Models\Source;
use Illuminate\Console\Command;

class ScrapeNewsCommand extends Command
{
    protected $signature   = 'scrape:news {--source= : Scrape a specific source by ID}';
    protected $description = 'Scrape all active news sources';

    public function handle(): void
    {
        $sources = Source::where('is_active', true)
            ->when($this->option('source'), fn($q) =>
                $q->where('id', $this->option('source'))
            )
            ->get();

        if ($sources->isEmpty()) {
            $this->warn('No active sources found.');
            return;
        }

        foreach ($sources as $source) {
            ScrapeSourceJob::dispatch($source);
            $this->info("✅ Job dispatched for: {$source->name}");
        }

        $this->info("🚀 Total: {$sources->count()} source(s) queued.");
    }
}