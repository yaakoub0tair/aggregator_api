<?php

namespace App\Services\Scrapers;

class HespressScraper extends BaseScraper
{
    public function scrape(): array
    {
        $saved  = 0;
        $found  = 0;
        $errors = [];

        try {
            $crawler = $this->fetchHtml($this->source->base_url);

            $crawler->filter('.card, .card-article')->each(function ($node) use (&$saved, &$found, &$errors) {
                try {
                    $linkNode = $node->filter('a')->first();
                    if ($linkNode->count() === 0) {
                        return;
                    }

                    $url = $linkNode->attr('href') ?? null;

                    // Skip tag/category pages, only article URLs ending with .html and numeric id
                    if (!$url || !preg_match('/\d+\.html$/', $url)) {
                        return;
                    }

                    $found++;

                    $title = $this->extractText($node, 'h2, h3, h4, .title, .card-title');
                    if ($title === '') {
                        $title = trim($linkNode->text(''));
                    }

                    $imageNode = $node->filter('img')->first();
                    $image     = $imageNode->count() ? $imageNode->attr('src') : null;

                    if (!$title) {
                        return;
                    }

                    if (!str_starts_with($url, 'http')) {
                        $url = rtrim($this->source->base_url, '/') . '/' . ltrim($url, '/');
                    }

                    $article = $this->saveArticle([
                        'title'        => $title,
                        'url'          => $url,
                        'image_url'    => $image,
                        'published_at' => now(),
                    ]);

                    if ($article) {
                        $saved++;
                    }
                } catch (\Exception $e) {
                    $errors[] = $e->getMessage();
                }
            });
        } catch (\Exception $e) {
            $errors[] = $e->getMessage();
        }

        return compact('found', 'saved', 'errors');
    }
}
