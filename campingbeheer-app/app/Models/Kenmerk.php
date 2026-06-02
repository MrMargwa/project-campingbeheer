<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Kenmerk extends Model
{
    protected $fillable = [
        'naam',
    ];

    public const CREATED_AT = 'aangemaakt_op';
    public const UPDATED_AT = null;

    public function verblijven(): BelongsToMany
    {
        return $this->belongsToMany(Accommodatie::class, 'accommodatie_kenmerk', 'kenmerk_id', 'accommodatie_id');
    }
}