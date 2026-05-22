<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\Article;
use App\Models\Category;
use App\Models\ScrapingJob;
use App\Models\Source;
use App\Models\User;

class DashboardController extends Controller
{
    /**
     * @OA\Get(
     *     path="/admin/stats",
     *     tags={"Admin Dashboard"},
     *     summary="Get dashboard statistics",
     *     description="Returns totals, source status, scraping metrics, and article counts",
     *     security={{"bearerAuth":{}}},
     *     @OA\Response(
     *         response=200,
     *         description="Dashboard statistics",
     *         @OA\JsonContent(ref="#/components/schemas/StatsResponse")
     *     ),
     *     @OA\Response(response=401, description="Unauthenticated", @OA\JsonContent(ref="#/components/schemas/UnauthorizedError")),
     *     @OA\Response(response=403, description="Forbidden", @OA\JsonContent(ref="#/components/schemas/ForbiddenError"))
     * )
     */
    public function stats()
    {
        $lastJob = ScrapingJob::with('source')
            ->latest()
            ->first();

        return response()->json([
            'totals' => [
                'articles'   => Article::count(),
                'sources'    => Source::count(),
                'categories' => Category::count(),
                'users'      => User::count(),
            ],
            'sources' => [
                'active'   => Source::where('is_active', true)->count(),
                'inactive' => Source::where('is_active', false)->count(),
            ],
            'scraping' => [
                'total_jobs'      => ScrapingJob::count(),
                'completed_jobs'  => ScrapingJob::where('status', 'completed')->count(),
                'failed_jobs'     => ScrapingJob::where('status', 'failed')->count(),
                'last_run_at'     => $lastJob?->finished_at,
                'last_run_source' => $lastJob?->source?->name,
                'last_run_status' => $lastJob?->status,
            ],
            'articles' => [
                'today'      => Article::whereDate('created_at', today())->count(),
                'this_week'  => Article::whereBetween('created_at', [now()->startOfWeek(), now()])->count(),
                'this_month' => Article::whereMonth('created_at', now()->month)->count(),
            ],
        ]);
    }

    /**
     * @OA\Get(
     *     path="/admin/logs",
     *     tags={"Admin Dashboard"},
     *     summary="Get admin scraping logs",
     *     description="Paginated scraping jobs with source and log (same as /scrape/logs)",
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(name="page", in="query", description="Page number", @OA\Schema(type="integer", example=1)),
     *     @OA\Response(
     *         response=200,
     *         description="Paginated scraping logs",
     *         @OA\JsonContent(ref="#/components/schemas/PaginatedScrapingJobs")
     *     ),
     *     @OA\Response(response=401, description="Unauthenticated", @OA\JsonContent(ref="#/components/schemas/UnauthorizedError")),
     *     @OA\Response(response=403, description="Forbidden", @OA\JsonContent(ref="#/components/schemas/ForbiddenError"))
     * )
     */
    public function logs()
    {
        $logs = ScrapingJob::with(['source', 'log'])
            ->latest()
            ->paginate(20);

        return response()->json($logs);
    }

    /**
     * @OA\Get(
     *     path="/admin/articles-per-source",
     *     tags={"Admin Dashboard"},
     *     summary="Articles count per source",
     *     security={{"bearerAuth":{}}},
     *     @OA\Response(
     *         response=200,
     *         description="Articles per source breakdown",
     *         @OA\JsonContent(type="array", @OA\Items(ref="#/components/schemas/ArticlesPerSourceItem"))
     *     ),
     *     @OA\Response(response=401, description="Unauthenticated", @OA\JsonContent(ref="#/components/schemas/UnauthorizedError")),
     *     @OA\Response(response=403, description="Forbidden", @OA\JsonContent(ref="#/components/schemas/ForbiddenError"))
     * )
     */
    public function articlesPerSource()
    {
        $data = Source::withCount('articles')
            ->get()
            ->map(fn ($source) => [
                'source'         => $source->name,
                'articles_count' => $source->articles_count,
                'is_active'      => $source->is_active,
            ]);

        return response()->json($data);
    }

    /**
     * @OA\Get(
     *     path="/admin/articles-per-category",
     *     tags={"Admin Dashboard"},
     *     summary="Articles count per category",
     *     security={{"bearerAuth":{}}},
     *     @OA\Response(
     *         response=200,
     *         description="Articles per category breakdown",
     *         @OA\JsonContent(type="array", @OA\Items(ref="#/components/schemas/ArticlesPerCategoryItem"))
     *     ),
     *     @OA\Response(response=401, description="Unauthenticated", @OA\JsonContent(ref="#/components/schemas/UnauthorizedError")),
     *     @OA\Response(response=403, description="Forbidden", @OA\JsonContent(ref="#/components/schemas/ForbiddenError"))
     * )
     */
    public function articlesPerCategory()
    {
        $data = Category::withCount('articles')
            ->get()
            ->map(fn ($cat) => [
                'category'       => $cat->name,
                'slug'           => $cat->slug,
                'articles_count' => $cat->articles_count,
            ]);

        return response()->json($data);
    }

    /**
     * @OA\Get(
     *     path="/admin/recent-articles",
     *     tags={"Admin Dashboard"},
     *     summary="Get 10 most recent articles",
     *     security={{"bearerAuth":{}}},
     *     @OA\Response(
     *         response=200,
     *         description="Recent articles with category and source",
     *         @OA\JsonContent(type="array", @OA\Items(ref="#/components/schemas/ArticleResource"))
     *     ),
     *     @OA\Response(response=401, description="Unauthenticated", @OA\JsonContent(ref="#/components/schemas/UnauthorizedError")),
     *     @OA\Response(response=403, description="Forbidden", @OA\JsonContent(ref="#/components/schemas/ForbiddenError"))
     * )
     */
    public function recentArticles()
    {
        $articles = Article::with('category', 'source')
            ->latest('published_at')
            ->take(10)
            ->get();

        return response()->json($articles);
    }

    /**
     * @OA\Put(
     *     path="/admin/sources/{source}/toggle",
     *     tags={"Admin Dashboard"},
     *     summary="Toggle source active status",
     *     description="Activates or deactivates a news source for scraping",
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(name="source", in="path", required=true, description="Source ID", @OA\Schema(type="integer", example=1)),
     *     @OA\Response(
     *         response=200,
     *         description="Source toggled",
     *         @OA\JsonContent(ref="#/components/schemas/ToggleSourceResponse")
     *     ),
     *     @OA\Response(response=401, description="Unauthenticated", @OA\JsonContent(ref="#/components/schemas/UnauthorizedError")),
     *     @OA\Response(response=403, description="Forbidden", @OA\JsonContent(ref="#/components/schemas/ForbiddenError")),
     *     @OA\Response(response=404, description="Source not found", @OA\JsonContent(ref="#/components/schemas/NotFoundError"))
     * )
     */
    public function toggleSource(Source $source)
    {
        $source->update(['is_active' => ! $source->is_active]);

        return response()->json([
            'message'   => "Source {$source->name} is now ".($source->is_active ? 'active' : 'inactive'),
            'is_active' => $source->is_active,
        ]);
    }
}
