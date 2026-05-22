<?php

namespace Database\Seeders;

use App\Models\Source;
use Illuminate\Database\Seeder;

class SourceSeeder extends Seeder
{
    public function run(): void
    {
        Source::insert([
            [
                'name'          => 'Hespress',
                'base_url'      => 'https://www.hespress.com',
                'scraper_class' => 'HespressScraper',
                'is_active'     => true,
                'created_at'    => now(),
                'updated_at'    => now(),
            ],
            [
                'name'          => 'Hibapress',
                'base_url'      => 'https://ar.hibapress.com',
                'scraper_class' => 'HibapressScraper',
                'is_active'     => true,
                'created_at'    => now(),
                'updated_at'    => now(),
            ],
        ]);
    }
}
