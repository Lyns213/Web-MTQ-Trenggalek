<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Official extends Model
{
    use HasFactory;

    public const ROLE_DEWAN_HAKIM = 'dewan_hakim';
    public const ROLE_PANITERA = 'panitera';
    public const ROLE_PANITIA = 'panitia';

    protected $fillable = [
        'role',
        'nama',
        'jabatan',
        'foto',
        'tahun_id',
    ];

    public function tahun(): BelongsTo
    {
        return $this->belongsTo(Tahun::class);
    }
}
