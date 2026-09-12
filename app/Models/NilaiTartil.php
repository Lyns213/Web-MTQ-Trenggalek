<?php

namespace App\Models;

use App\Models\Peserta;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class NilaiTartil extends Model
{
    use HasFactory;

    public function peserta(): BelongsTo
    {
        return $this->belongsTo(Peserta::class);
    }
}
