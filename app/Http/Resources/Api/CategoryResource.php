<?php

namespace App\Http\Resources\Api;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CategoryResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $locale = $request->header('Accept-Language', config('app.locale'));
        $locale = $request->query('locale', $locale);

        $translation = $this->translations()
            ->where('locale', $locale)
            ->first();

        // Fallback to default locale if translation not found
        if (! $translation) {
            $translation = $this->translations()
                ->where('locale', config('app.locale'))
                ->first();
        }

        return [
            'id' => $this->id,
            'slug' => $translation?->slug ?? $this->slug_base,
            'sort_order' => $this->sort_order,
            'translation' => [
                'locale' => $translation?->locale,
                'name' => $translation?->name,
                'slug' => $translation?->slug,
                'description' => $translation?->description,
            ],
            'posts_count' => $this->when(isset($this->posts_count), $this->posts_count),
            'posts' => PostResource::collection($this->whenLoaded('posts')),
            'created_at' => $this->created_at?->toISOString(),
            'updated_at' => $this->updated_at?->toISOString(),
        ];
    }
}
