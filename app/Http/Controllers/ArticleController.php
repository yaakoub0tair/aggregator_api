<?php

namespace App\Http\Controllers;

use App\Models\Article;
use App\Http\Resources\ArticleResource;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class ArticleController extends Controller
{
    // GET /api/articles
    public function index(Request $request)
    {
        $articles = Article::with('category', 'source')
            ->when($request->category, fn($q) =>
                $q->whereHas('category', fn($q) =>
                    $q->where('slug', $request->category)
                )
            )
            ->when($request->source, fn($q) =>
                $q->where('source_id', $request->source)
            )
            ->latest('published_at')
            ->paginate(15);

        return ArticleResource::collection($articles);
    }

    // GET /api/articles/{slug}
    public function show(string $slug)
    {
        $article = Article::with('category', 'source')
            ->where('slug', $slug)
            ->firstOrFail();

        return new ArticleResource($article);
    }

    // POST /api/articles
    public function store(Request $request)
    {
        $validated = $request->validate([
            'title'        => 'required|string',
            'summary'      => 'nullable|string',
            'image_url'    => 'nullable|url',
            'url'          => 'required|url|unique:articles',
            'published_at' => 'nullable|date',
            'category_id'  => 'nullable|exists:categories,id',
            'source_id'    => 'nullable|exists:sources,id',
        ]);

        $validated['slug'] = Str::slug($validated['title']) . '-' . uniqid();

        $article = Article::create($validated);
        return new ArticleResource($article->load('category', 'source'));
    }

    // DELETE /api/articles/{id}
    public function destroy(Article $article)
    {
        $article->delete();
        return response()->json(['message' => 'Article deleted']);
    }

    // GET /api/search?q=keyword
    public function search(Request $request)
    {
        $request->validate(['q' => 'required|string|min:2']);

        $articles = Article::with('category', 'source')
            ->where('title', 'LIKE', "%{$request->q}%")
            ->orWhere('summary', 'LIKE', "%{$request->q}%")
            ->latest('published_at')
            ->paginate(15);

        return ArticleResource::collection($articles);
    }
}