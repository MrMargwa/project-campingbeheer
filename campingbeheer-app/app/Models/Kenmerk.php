<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Kenmerk extends Model
{
    protected $table = 'features';

    protected $fillable = [
        'name',
        'name_en', 'name_de', 'name_fy',
    ];

    public function accommodations(): BelongsToMany
    {
        return $this->belongsToMany(Accommodatie::class, 'accommodation_feature', 'feature_id', 'accommodation_id');
    }

    public function translatedName(string $locale): string
    {
        $col = 'name_' . $locale;
        return $this->$col ?: $this->name;
    }
}
