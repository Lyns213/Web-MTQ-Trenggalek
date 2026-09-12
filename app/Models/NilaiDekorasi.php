<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;


class NilaiDekorasi extends Model
{
    use HasFactory;

    public function peserta(): BelongsTo
    {
        return $this->belongsTo(Peserta::class);
    }
}
