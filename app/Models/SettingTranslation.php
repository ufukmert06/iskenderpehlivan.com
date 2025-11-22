<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SettingTranslation extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'setting_id',
        'locale',
        'site_name',
        'site_description',
        'footer_text',
        'about_welcome_title',
        'about_welcome_description',
        'about_mission_title',
        'about_mission_content',
        'about_vision_title',
        'about_vision_content',
        'counter_years_label',
        'counter_customers_label',
        'counter_sessions_label',
        'counter_certifications_label',
    ];

    /**
     * Get the setting that owns the translation.
     */
    public function setting(): BelongsTo
    {
        return $this->belongsTo(Setting::class);
    }
}
