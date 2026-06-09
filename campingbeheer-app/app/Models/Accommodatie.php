<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Accommodatie extends Model
{
    protected $fillable = [
        'titel',
        'titel_en', 'titel_de', 'titel_fy',
        'type',
        'type_en', 'type_de', 'type_fy',
        'beschrijving',
        'beschrijving_en', 'beschrijving_de', 'beschrijving_fy',
        'min_personen',
        'max_personen',
        'prijs_per_nacht',
        'afbeelding',
        'latitude',
        'longitude',
        'status',
    ];

    public const CREATED_AT = 'aangemaakt_op';
    public const UPDATED_AT = 'bewerkt_op';

    public function boekingen(): HasMany
    {
        return $this->hasMany(Boeking::class, 'accommodatie_id');
    }

    public function kenmerken(): BelongsToMany
    {
        return $this->belongsToMany(Kenmerk::class, 'accommodatie_kenmerk', 'accommodatie_id', 'kenmerk_id');
    }

    public function vertaaldeTitel(string $locale): string
    {
        $col = 'titel_' . $locale;
        return $this->$col ?: $this->titel;
    }

    public function vertaaldeBeschrijving(string $locale): string
    {
        $col = 'beschrijving_' . $locale;
        return $this->$col ?: $this->beschrijving;
    }

    public function vertaaldType(string $locale): string
    {
        $col = 'type_' . $locale;
        return $this->$col ?: $this->type;
    }
}
