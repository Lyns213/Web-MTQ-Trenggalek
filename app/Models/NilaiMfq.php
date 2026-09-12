<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class NilaiMfq extends Model
{
    use HasFactory;

    public function grup(): BelongsTo
    {
        return $this->belongsTo(Grup::class);
    }
}