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
    ];

    /**
     * Get the setting that owns the translation.
     */
    public function setting(): BelongsTo
    {
        return $this->belongsTo(Setting::class);
    }
}
