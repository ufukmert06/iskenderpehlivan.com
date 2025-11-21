<?php

namespace App\Http\Resources\Api;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class SettingResource extends JsonResource
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
            'logo' => $this->logo ? asset('storage/'.$this->logo) : null,
            'favicon' => $this->favicon ? asset('storage/'.$this->favicon) : null,
            'contact' => [
                'email' => $this->contact_email,
                'phone' => $this->contact_phone,
                'address' => $this->contact_address,
            ],
            'social_media' => [
                'facebook' => $this->facebook,
                'twitter' => $this->twitter,
                'instagram' => $this->instagram,
                'linkedin' => $this->linkedin,
                'youtube' => $this->youtube,
            ],
            'maintenance_mode' => (bool) $this->maintenance_mode,
            'translation' => [
                'locale' => $translation?->locale,
                'site_name' => $translation?->site_name,
                'site_description' => $translation?->site_description,
                'footer_text' => $translation?->footer_text,
            ],
        ];
    }
}
