<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AccommodatieKenmerk extends Model
{
    protected $table = 'accommodation_feature';

    public $timestamps = false;

    protected $fillable = [
        'accommodation_id',
        'feature_id',
    ];

    public function accommodation(): BelongsTo
    {
        return $this->belongsTo(Accommodatie::class, 'accommodation_id');
    }

    public function feature(): BelongsTo
    {
        return $this->belongsTo(Kenmerk::class, 'feature_id');
    }
}
