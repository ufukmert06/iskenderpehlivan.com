<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Appointment extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'phone',
        'service_id',
        'preferred_date',
        'preferred_time',
        'message',
        'status',
        'ip_address',
        'user_agent',
    ];

    /**
     * Get attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'preferred_date' => 'date',
            'created_at' => 'datetime',
            'updated_at' => 'datetime',
        ];
    }

    /**
     * Get the service that the appointment is for.
     */
    public function service(): BelongsTo
    {
        return $this->belongsTo(Post::class, 'service_id');
    }
}
