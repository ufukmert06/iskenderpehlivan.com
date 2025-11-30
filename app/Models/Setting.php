<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Setting extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'logo',
        'dark_logo',
        'favicon',
        'contact_email',
        'contact_phone',
        'contact_address',
        'google_maps_url',
        'whatsapp',
        'facebook',
        'twitter',
        'instagram',
        'linkedin',
        'youtube',
        'rcc_number',
        'professional_title',
        'years_of_experience',
        'rating',
        'credentials',
        'therapeutic_approach',
        'maintenance_mode',
        'happy_customers',
        'therapy_sessions',
        'certifications_awards',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'maintenance_mode' => 'boolean',
            'years_of_experience' => 'integer',
            'happy_customers' => 'integer',
            'therapy_sessions' => 'integer',
            'certifications_awards' => 'integer',
        ];
    }

    /**
     * Get the translations for the setting.
     */
    public function translations(): HasMany
    {
        return $this->hasMany(SettingTranslation::class);
    }

    /**
     * Get translation for a specific locale.
     */
    public function translation(?string $locale = null): ?SettingTranslation
    {
        $locale = $locale ?? app()->getLocale();

        return $this->translations()->where('locale', $locale)->first();
    }
}
