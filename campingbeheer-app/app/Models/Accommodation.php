<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Accommodation extends Model
{
    protected $table = 'accommodations';

    protected $fillable = [
        'title',
        'type',
        'description',
        'min_persons',
        'max_persons',
        'price_per_night',
        'image',
        'latitude',
        'longitude',
        'status',
    ];

    public function bookings(): HasMany
    {
        return $this->hasMany(Booking::class, 'accommodation_id');
    }

    public function features(): BelongsToMany
    {
        return $this->belongsToMany(Feature::class, 'accommodation_feature', 'accommodation_id', 'feature_id');
    }

    public function translatedTitle(string $locale): string
    {
        $col = 'title_' . $locale;
        return $this->$col ?: $this->title;
    }

    public function translatedDescription(string $locale): string
    {
        $col = 'description_' . $locale;
        return $this->$col ?: $this->description;
    }

    public function translatedType(string $locale): string
    {
        $col = 'type_' . $locale;
        return $this->$col ?: $this->type;
    }
}
