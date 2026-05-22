<?php

namespace App\Services\Scrapers;

use App\Models\Source;
use Exception;

class ScraperFactory
{
    public static function make(Source $source): BaseScraper
    {
        $class = "App\\Services\\Scrapers\\" . $source->scraper_class;

        if (!class_exists($class)) {
            throw new Exception("Scraper class {$class} not found.");
        }

        return new $class($source);
    }
}