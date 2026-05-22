<?php

namespace App\Http\Controllers;

use App\Jobs\ScrapeSourceJob;
use App\Models\ScrapingJob;
use App\Models\Source;

class ScrapingController extends Controller
{
    /**
     * @OA\Post(
     *     path="/scrape/{source}",
     *     tags={"Scraping"},
     *     summary="Trigger scraping job for a source (Admin)",
     *     description="Dispatches a queue job to scrape articles from the given news source",
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(name="source", in="path", required=true, description="Source ID", @OA\Schema(type="integer", example=1)),
     *     @OA\Response(
     *         response=202,
     *         description="Scraping job dispatched",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="message", type="string", example="Scraping job dispatched for Hespress")
     *         )
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Source is inactive",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="message", type="string", example="Source is inactive")
     *         )
     *     ),
     *     @OA\Response(response=401, description="Unauthenticated", @OA\JsonContent(ref="#/components/schemas/UnauthorizedError")),
     *     @OA\Response(response=403, description="Forbidden", @OA\JsonContent(ref="#/components/schemas/ForbiddenError")),
     *     @OA\Response(response=404, description="Source not found", @OA\JsonContent(ref="#/components/schemas/NotFoundError"))
     * )
     */
    public function run(Source $source)
    {
        if (! $source->is_active) {
            return response()->json(['message' => 'Source is inactive'], 400);
        }

        ScrapeSourceJob::dispatch($source);

        return response()->json([
            'message' => "Scraping job dispatched for {$source->name}",
        ], 202);
    }

    /**
     * @OA\Get(
     *     path="/scrape/logs",
     *     tags={"Scraping"},
     *     summary="Get scraping job logs (Admin)",
     *     description="Returns paginated scraping jobs with their source and log details",
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
}
