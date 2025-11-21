<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\Api\SettingResource;
use App\Models\Setting;
use App\Traits\CachesApiResponses;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

/**
 * @tags Settings
 */
class SettingController extends Controller
{
    use CachesApiResponses;

    /**
     * Get site settings
     *
     * Returns global site settings including logo, contact info, social media links, and translations.
     *
     * @group Settings
     *
     * @queryParam locale string Language code for translations (tr, en). Default: tr. Example: tr
     *
     * @response 200 {
     *   "data": {
     *     "logo": "http://localhost/storage/settings/logo.png",
     *     "favicon": "http://localhost/storage/settings/favicon.ico",
     *     "contact": {
     *       "email": "info@example.com",
     *       "phone": "+90 555 123 4567",
     *       "address": "Istanbul, Turkey"
     *     },
     *     "social_media": {
     *       "facebook": "https://facebook.com/example",
     *       "twitter": "https://twitter.com/example",
     *       "instagram": "https://instagram.com/example",
     *       "linkedin": "https://linkedin.com/company/example",
     *       "youtube": "https://youtube.com/@example"
     *     },
     *     "maintenance_mode": false,
     *     "translation": {
     *       "locale": "tr",
     *       "site_name": "My Site",
     *       "site_description": "A great website",
     *       "footer_text": "© 2025 My Site. All rights reserved."
     *     }
     *   }
     * }
     *
     * @response 404 {
     *   "message": "Settings not found"
     * }
     */
    public function index(Request $request): SettingResource|Response
    {
        $locale = $request->query('locale', $request->header('Accept-Language', config('app.locale')));
        $version = $this->getCacheVersion('settings');

        $cacheKey = $this->getCacheKey("api_v{$version}_settings", [
            'locale' => $locale,
        ]);

        return $this->cacheResponse($cacheKey, function () use ($request) {
            $setting = Setting::with(['translations'])->first();

            if (! $setting) {
                return response()->json(['message' => 'Settings not found'], 404);
            }

            return new SettingResource($setting);
        });
    }
}
