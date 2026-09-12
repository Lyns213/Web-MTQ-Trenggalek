<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Utusan extends Model
{
    use HasFactory;

    public function peserta(): HasMany
    {
        return $this->hasMany(Peserta::class);
    }

    public function user(): HasOne
    {
        return $this->hasOne(User::class);
    }

    public function grup(): HasMany
    {
        return $this->hasMany(Grup::class);
    }
}
