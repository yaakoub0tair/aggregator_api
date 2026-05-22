<?php

namespace App\Http\Controllers;

use App\Models\Source;
use App\Http\Resources\SourceResource;
use Illuminate\Http\Request;

class SourceController extends Controller
{
    // GET /api/sources
    public function index()
    {
        return SourceResource::collection(Source::all());
    }

    // POST /api/sources
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name'          => 'required|string',
            'base_url'      => 'required|url',
            'scraper_class' => 'required|string',
            'is_active'     => 'boolean',
        ]);

        $source = Source::create($validated);
        return new SourceResource($source);
    }

    // GET /api/sources/{id}
    public function show(Source $source)
    {
        return new SourceResource($source);
    }

    // PUT /api/sources/{id}
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

    // DELETE /api/sources/{id}
    public function destroy(Source $source)
    {
        $source->delete();
        return response()->json(['message' => 'Source deleted']);
    }
}