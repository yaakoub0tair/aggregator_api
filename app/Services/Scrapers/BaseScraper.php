<?php

namespace App\Services\Scrapers;

use App\Models\Article;
use App\Models\Source;
use GuzzleHttp\Exception\GuzzleException;
use Illuminate\Support\Str;
use Symfony\Component\DomCrawler\Crawler;
use GuzzleHttp\Client;

abstract class BaseScraper
{
    protected Client $client;
    protected Source $source;

    public function __construct(Source $source)
    {
        $this->source = $source;
        $this->client = new Client([
            'timeout' => 15,
            'verify'  => false,
            'headers' => [
                'User-Agent' => 'Mozilla/5.0 (compatible; NewsBot/1.0)',
            ],
        ]);
    }

    abstract public function scrape(): array;

    protected function fetchHtml(string $url): Crawler
    {
        try {
            $response = $this->client->get($url);
            return new Crawler($response->getBody()->getContents());
        } catch (GuzzleException $e) {
            throw new \RuntimeException("Failed to fetch {$url}: {$e->getMessage()}", 0, $e);
        }
    }

    protected function isDuplicate(string $url): bool
    {
        return Article::where('url', $url)->exists();
    }

    protected function saveArticle(array $data): ?Article
    {
        if ($this->isDuplicate($data['url'])) {
            return null;
        }

        return Article::create([
            'title'        => $data['title'],
            'slug'         => Str::slug($data['title']) . '-' . uniqid(),
            'summary'      => $data['summary'] ?? null,
            'image_url'    => $data['image_url'] ?? null,
            'url'          => $data['url'],
            'published_at' => $data['published_at'] ?? now(),
            'category_id'  => $data['category_id'] ?? null,
            'source_id'    => $this->source->id,
        ]);
    }

    protected function extractText(Crawler $node, string $selector): string
    {
        $filtered = $node->filter($selector);

        if ($filtered->count() === 0) {
            return '';
        }

        return trim($filtered->first()->text(''));
    }
}
