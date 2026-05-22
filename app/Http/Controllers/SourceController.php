<?php

namespace App\Http\Controllers;

use App\Http\Resources\SourceResource;
use App\Models\Source;
use Illuminate\Http\Request;

class SourceController extends Controller
{
    /**
     * @OA\Get(
     *     path="/sources",
     *     tags={"Sources"},
     *     summary="List all sources",
     *     @OA\Response(
     *         response=200,
     *         description="List of sources",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="data", type="array", @OA\Items(ref="#/components/schemas/SourceResource"))
     *         )
     *     )
     * )
     */
    public function index()
    {
        return SourceResource::collection(Source::all());
    }

    /**
     * @OA\Post(
     *     path="/sources",
     *     tags={"Sources"},
     *     summary="Create source (Admin)",
     *     security={{"bearerAuth":{}}},
     *     @OA\RequestBody(required=true, @OA\JsonContent(ref="#/components/schemas/StoreSourceRequest")),
     *     @OA\Response(
     *         response=201,
     *         description="Source created",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="data", ref="#/components/schemas/SourceResource")
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
            'name'          => 'required|string',
            'base_url'      => 'required|url',
            'scraper_class' => 'required|string',
            'is_active'     => 'boolean',
        ]);

        $source = Source::create($validated);

        return (new SourceResource($source))
            ->response()
            ->setStatusCode(201);
    }

    /**
     * @OA\Put(
     *     path="/sources/{source}",
     *     tags={"Sources"},
     *     summary="Update source (Admin)",
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(name="source", in="path", required=true, description="Source ID", @OA\Schema(type="integer", example=1)),
     *     @OA\RequestBody(required=true, @OA\JsonContent(ref="#/components/schemas/UpdateSourceRequest")),
     *     @OA\Response(
     *         response=200,
     *         description="Source updated",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="data", ref="#/components/schemas/SourceResource")
     *         )
     *     ),
     *     @OA\Response(response=401, description="Unauthenticated", @OA\JsonContent(ref="#/components/schemas/UnauthorizedError")),
     *     @OA\Response(response=403, description="Forbidden", @OA\JsonContent(ref="#/components/schemas/ForbiddenError")),
     *     @OA\Response(response=404, description="Source not found", @OA\JsonContent(ref="#/components/schemas/NotFoundError")),
     *     @OA\Response(response=422, description="Validation error", @OA\JsonContent(ref="#/components/schemas/ValidationError"))
     * )
     */
    public function update(Request $request, Source $source)
    {
        $validated = $request->validate([
            'name'          => 'string',
            'base_url'      => 'url',
            'scraper_class' => 'string',
            'is_active'     => 'boolean',
        ]);

        $source->update($validated);

        return new SourceResource($source);
    }

    /**
     * @OA\Delete(
     *     path="/sources/{source}",
     *     tags={"Sources"},
     *     summary="Delete source (Admin)",
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(name="source", in="path", required=true, description="Source ID", @OA\Schema(type="integer", example=1)),
     *     @OA\Response(response=200, description="Source deleted", @OA\JsonContent(ref="#/components/schemas/MessageResponse")),
     *     @OA\Response(response=401, description="Unauthenticated", @OA\JsonContent(ref="#/components/schemas/UnauthorizedError")),
     *     @OA\Response(response=403, description="Forbidden", @OA\JsonContent(ref="#/components/schemas/ForbiddenError")),
     *     @OA\Response(response=404, description="Source not found", @OA\JsonContent(ref="#/components/schemas/NotFoundError"))
     * )
     */
    public function destroy(Source $source)
    {
        $source->delete();

        return response()->json(['message' => 'Source deleted']);
    }
}
