<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Grup extends Model
{
    use HasFactory;

    public function tahun(): BelongsTo
    {
        return $this->belongsTo(Tahun::class);
    }

    public function nilaimfq(): HasOne
    {
        return $this->hasOne(NilaiMfq::class);
    }

    public function peserta(): HasMany
    {
        return $this->hasMany(Peserta::class);
    }

    public function utusan(): BelongsTo
    {
        return $this->belongsTo(Utusan::class, 'utusan_id');
    }
}
