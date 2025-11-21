<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ServiceTranslation extends Model
{
    /** @use HasFactory<\Database\Factories\ServiceTranslationFactory> */
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'service_id',
        'locale',
        'name',
        'description',
    ];

    /**
     * Get the service that owns the translation.
     */
    public function service(): BelongsTo
    {
        return $this->belongsTo(Service::class);
    }
}
