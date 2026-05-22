<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class SourceResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'id'            => $this->id,
            'name'          => $this->name,
            'base_url'      => $this->base_url,
            'scraper_class' => $this->scraper_class,
            'is_active'     => $this->is_active,
            'created_at'    => $this->created_at,
        ];
    }
}