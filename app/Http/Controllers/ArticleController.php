<?php

namespace App\Http\Controllers;

use App\Http\Resources\ArticleResource;
use App\Models\Article;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;

class ArticleController extends Controller
{
    /**
     * @OA\Get(
     *     path="/articles",
     *     tags={"Articles"},
     *     summary="List articles (paginated)",
     *     description="Returns a paginated list of articles. Cached for 5 minutes.",
     *     @OA\Parameter(name="category", in="query", description="Filter by category slug", @OA\Schema(type="string", example="siasa")),
     *     @OA\Parameter(name="source", in="query", description="Filter by source ID", @OA\Schema(type="integer", example=1)),
     *     @OA\Parameter(name="page", in="query", description="Page number", @OA\Schema(type="integer", example=1)),
     *     @OA\Response(
     *         response=200,
     *         description="Paginated articles",
     *         @OA\JsonContent(ref="#/components/schemas/PaginatedArticles")
     *     )
     * )
     */
    public function index(Request $request)
    {
        $cacheKey = 'articles.'.md5($request->fullUrl());

        $articles = Cache::remember($cacheKey, now()->addMinutes(5), function () use ($request) {
            return Article::with('category', 'source')
                ->when($request->category, fn ($q) =>
                    $q->whereHas('category', fn ($q) =>
                        $q->where('slug', $request->category)
                    )
                )
                ->when($request->source, fn ($q) =>
                    $q->where('source_id', $request->source)
                )
                ->latest('published_at')
                ->paginate(15);
        });

        return ArticleResource::collection($articles);
    }

    /**
     * @OA\Get(
     *     path="/articles/{slug}",
     *     tags={"Articles"},
     *     summary="Get single article by slug",
     *     @OA\Parameter(name="slug", in="path", required=true, description="Article slug", @OA\Schema(type="string", example="almghrb-ytahl-lkas-alalam-abc123")),
     *     @OA\Response(
     *         response=200,
     *         description="Article details",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="data", ref="#/components/schemas/ArticleResource")
     *         )
     *     ),
     *     @OA\Response(response=404, description="Article not found", @OA\JsonContent(ref="#/components/schemas/NotFoundError"))
     * )
     */
    public function show(string $slug)
    {
        $article = Article::with('category', 'source')
            ->where('slug', $slug)
            ->firstOrFail();

        return new ArticleResource($article);
    }

    /**
     * @OA\Post(
     *     path="/articles",
     *     tags={"Articles"},
     *     summary="Create article (Admin)",
     *     security={{"bearerAuth":{}}},
     *     @OA\RequestBody(required=true, @OA\JsonContent(ref="#/components/schemas/StoreArticleRequest")),
     *     @OA\Response(
     *         response=201,
     *         description="Article created",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="data", ref="#/components/schemas/ArticleResource")
     *         )
     *     ),
     *     @OA\Response(response=401, description="Unauthenticated", @OA\JsonContent(ref="#/components/schemas/UnauthorizedError")),
     *     @OA\Response(response=403, description="Forbidden", @OA\JsonContent(ref="#/components/schemas/ForbiddenError")),
     *     @OA\Response(response=422, description="Validation error", @OA\JsonContent(ref="#/components/schemas/ValidationError"))
     * )
     */
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

        $validated['slug'] = Str::slug($validated['title']).'-'.uniqid();

        $article = Article::create($validated);

        Cache::flush();

        return (new ArticleResource($article->load('category', 'source')))
            ->response()
            ->setStatusCode(201);
    }

    /**
     * @OA\Delete(
     *     path="/articles/{article}",
     *     tags={"Articles"},
     *     summary="Delete article (Admin)",
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(name="article", in="path", required=true, description="Article ID", @OA\Schema(type="integer", example=1)),
     *     @OA\Response(response=200, description="Article deleted", @OA\JsonContent(ref="#/components/schemas/MessageResponse")),
     *     @OA\Response(response=401, description="Unauthenticated", @OA\JsonContent(ref="#/components/schemas/UnauthorizedError")),
     *     @OA\Response(response=403, description="Forbidden", @OA\JsonContent(ref="#/components/schemas/ForbiddenError")),
     *     @OA\Response(response=404, description="Article not found", @OA\JsonContent(ref="#/components/schemas/NotFoundError"))
     * )
     */
    public function destroy(Article $article)
    {
        $article->delete();

        Cache::flush();

        return response()->json(['message' => 'Article deleted']);
    }

    /**
     * @OA\Get(
     *     path="/search",
     *     tags={"Articles"},
     *     summary="Search articles",
     *     description="Search articles by title or summary (minimum 2 characters)",
     *     @OA\Parameter(name="q", in="query", required=true, description="Search keyword", @OA\Schema(type="string", example="المغرب")),
     *     @OA\Parameter(name="page", in="query", description="Page number", @OA\Schema(type="integer", example=1)),
     *     @OA\Response(
     *         response=200,
     *         description="Search results",
     *         @OA\JsonContent(ref="#/components/schemas/PaginatedArticles")
     *     ),
     *     @OA\Response(response=422, description="Validation error", @OA\JsonContent(ref="#/components/schemas/ValidationError"))
     * )
     */
    public function search(Request $request)
    {
        $request->validate(['q' => 'required|string|min:2']);

        $articles = Article::with('category', 'source')
            ->where(function ($q) use ($request) {
                $q->where('title', 'LIKE', "%{$request->q}%")
                  ->orWhere('summary', 'LIKE', "%{$request->q}%");
            })
            ->latest('published_at')
            ->paginate(15);

        return ArticleResource::collection($articles);
    }
}
