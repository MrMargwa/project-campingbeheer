<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AccommodatieKenmerk extends Model
{
    protected $table = 'accommodatie_kenmerk';

    public $timestamps = false;

    protected $fillable = [
        'accommodatie_id',
        'kenmerk_id',
    ];

    public function accommodatie(): BelongsTo
    {
        return $this->belongsTo(Accommodatie::class, 'accommodatie_id');
    }

    public function kenmerk(): BelongsTo
    {
        return $this->belongsTo(Kenmerk::class, 'kenmerk_id');
    }
}