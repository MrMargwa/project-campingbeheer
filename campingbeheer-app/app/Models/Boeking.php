<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Boeking extends Model
{
    protected $fillable = [
        'gebruiker_id',
        'accommodatie_id',
        'aankomst_datum',
        'vertrek_datum',
        'aantal_personen',
        'totaal_prijs',
        'status',
    ];

    public const CREATED_AT = 'aangemaakt_op';
    public const UPDATED_AT = 'bewerkt_op';

    public function gebruiker(): BelongsTo
    {
        return $this->belongsTo(User::class, 'gebruiker_id');
    }

    public function accommodatie(): BelongsTo
    {
        return $this->belongsTo(Accommodatie::class, 'accommodatie_id');
    }
}