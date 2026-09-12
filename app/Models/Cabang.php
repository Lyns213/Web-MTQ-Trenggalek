<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Carbon;

class Cabang extends Model
{
    use HasFactory;

    public function peserta(): HasMany
    {
        return $this->hasMany(Peserta::class);
    }

    // public function tahun(): BelongsTo
    // {
    //     return $this->belongsTo(Tahun::class);
    // }

    protected $dates = ['per_tanggal'];


}
