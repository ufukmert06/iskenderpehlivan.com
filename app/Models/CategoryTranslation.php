<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CategoryTranslation extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'category_id',
        'locale',
        'name',
        'description',
        'slug',
    ];

    /**
     * Get the category that owns the translation.
     */
    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }
}
