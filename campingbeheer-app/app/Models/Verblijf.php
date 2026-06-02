<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Verblijf extends Model
{
    protected $fillable = [
        'titel',
        'type',
        'beschrijving',
        'max_personen',
        'prijs_per_nacht',
        'afbeelding',
        'actief',
    ];

    public const CREATED_AT = 'aangemaakt_op';
    public const UPDATED_AT = 'bewerkt_op';

    public function boekingen(): HasMany
    {
        return $this->hasMany(Boeking::class, 'verblijf_id');
    }

    public function kenmerken(): BelongsToMany
    {
        return $this->belongsToMany(Kenmerk::class, 'verblijf_kenmerk', 'verblijf_id', 'kenmerk_id');
    }
}
