<?php

namespace App\Http\Resources\Api;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PostResource extends JsonResource
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
            'type' => $this->type,
            'slug' => $translation?->slug ?? $this->slug_base,
            'status' => $this->status,
            'featured_image' => $this->featured_image ? asset('storage/'.$this->featured_image) : null,
            'sort_order' => $this->sort_order,
            'author' => [
                'id' => $this->user?->id,
                'name' => $this->user?->name,
                'email' => $this->user?->email,
            ],
            'translation' => [
                'locale' => $translation?->locale,
                'title' => $translation?->title,
                'slug' => $translation?->slug,
                'content' => $translation?->content,
                'excerpt' => $translation?->excerpt,
                'meta_title' => $translation?->meta_title,
                'meta_description' => $translation?->meta_description,
                'meta_keywords' => $translation?->meta_keywords,
            ],
            'categories' => CategoryResource::collection($this->whenLoaded('categories')),
            'tags' => TagResource::collection($this->whenLoaded('tags')),
            'created_at' => $this->created_at?->toISOString(),
            'updated_at' => $this->updated_at?->toISOString(),
        ];
    }
}
