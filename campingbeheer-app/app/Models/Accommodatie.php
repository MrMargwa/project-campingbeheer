<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Accommodatie extends Model
{
    protected $fillable = [
        'titel',
        'type',
        'beschrijving',
        'min_personen',
        'max_personen',
        'prijs_per_nacht',
        'afbeelding',
        'coords',
        'status',
    ];

    public const CREATED_AT = 'aangemaakt_op';
    public const UPDATED_AT = 'bewerkt_op';

    public function boekingen(): HasMany
    {
        return $this->hasMany(Boeking::class, 'ccommodatie_id');
    }

    public function kenmerken(): BelongsToMany
    {
        return $this->belongsToMany(Kenmerk::class, 'accommodatie_kenmerk', 'accommodatie_id', 'kenmerk_id');
    }
}