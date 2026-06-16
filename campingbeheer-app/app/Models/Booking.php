<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Booking extends Model
{
    protected $table = 'bookings';

    protected $fillable = [
        'user_id',
        'accommodation_id',
        'name',
        'email',
        'phone',
        'postal_code',
        'house_number',
        'street',
        'city',
        'country',
        'arrival_date',
        'arrival_time',
        'departure_date',
        'departure_time',
        'number_of_persons',
        'notes',
        'total_price',
        'status',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function accommodation(): BelongsTo
    {
        return $this->belongsTo(Accommodation::class, 'accommodation_id');
    }
}
