<?php

namespace App\Services\Scrapers;

class HibapressScraper extends BaseScraper
{
    public function scrape(): array
    {
        $saved  = 0;
        $found  = 0;
        $errors = [];

        try {
            $crawler = $this->fetchHtml($this->source->base_url);

            $crawler->filter('article, .jeg_post, .post-item, .jnews_post')->each(function ($node) use (&$saved, &$found, &$errors) {
                try {
                    $found++;

                    $linkNode = $node->filter('a')->first();
                    if ($linkNode->count() === 0) {
                        return;
                    }

                    $url   = $linkNode->attr('href') ?? null;
                    $title = $this->extractText($node, 'h2, h3, .jeg_post_title, .jeg_post_title a');

                    $imgNode = $node->filter('img')->first();
                    $image   = $imgNode->count()
                        ? ($imgNode->attr('data-src') ?? $imgNode->attr('src'))
                        : null;

                    if (!$url || !$title) {
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
