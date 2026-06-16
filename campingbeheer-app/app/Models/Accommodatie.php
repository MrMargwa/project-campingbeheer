<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Accommodatie extends Model
{
    protected $table = 'accommodations';

    protected $fillable = [
        'titel',
        'type',
        'beschrijving',
        'min_personen',
        'max_personen',
        'huisdieren_toegestaan',
        'prijs_per_nacht',
        'afbeelding',
        'latitude',
        'longitude',
        'status',
    ];

    protected function casts(): array
    {
        return [
            'huisdieren_toegestaan' => 'boolean',
        ];
    }

    public const CREATED_AT = 'aangemaakt_op';
    public const UPDATED_AT = 'bewerkt_op';

    public function boekingen(): HasMany
    {
        return $this->hasMany(Boeking::class, 'accommodatie_id');
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
