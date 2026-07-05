<?php

namespace App\Console\Commands;

use App\Jobs\ScrapeSourceJob;
use App\Models\Source;
use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;

#[Signature('scrape:all')]
#[Description('Scrape all active news sources')]
class ScrapeAllSourcesCommand extends Command
{
    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Starting to scrape all active sources...');

        $sources = Source::where('is_active', true)->get();

        if ($sources->isEmpty()) {
            $this->warn('No active sources found.');
            return Command::SUCCESS;
        }

        $this->info("Found {$sources->count()} active source(s).");

        foreach ($sources as $source) {
            $this->info("Dispatching scraping job for: {$source->name}");
            ScrapeSourceJob::dispatch($source);
        }

        $this->info('All scraping jobs dispatched successfully.');

        return Command::SUCCESS;
    }
}
