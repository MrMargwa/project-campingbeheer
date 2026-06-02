<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class VerblijfKenmerk extends Model
{
    protected $table = 'verblijf_kenmerk';

    public $timestamps = false;

    protected $fillable = [
        'verblijf_id',
        'kenmerk_id',
    ];

    public function verblijf(): BelongsTo
    {
        return $this->belongsTo(Verblijf::class, 'verblijf_id');
    }

    public function kenmerk(): BelongsTo
    {
        return $this->belongsTo(Kenmerk::class, 'kenmerk_id');
    }
}
