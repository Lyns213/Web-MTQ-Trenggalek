<?php

namespace App\Models;

use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;


class Peserta extends Model
{
    use HasFactory;

    protected static function booted()
    {
        static::creating(function ($peserta) {
            $peserta->token = Str::random(64);  // Generate token acak

            if (is_null($peserta->pasfoto)) {
                // Tentukan gambar default berdasarkan jenis_kelamin
                if ($peserta->jenis_kelamin === 'putra') {
                    $peserta->pasfoto = 'default/defaultpa.png'; // Ganti dengan path gambar default untuk putra
                } elseif ($peserta->jenis_kelamin === 'putri') {
                    $peserta->pasfoto = 'default/defaultpi.png'; // Ganti dengan path gambar default untuk putri
                }
            }
        });
    }
    protected $casts = [
        'tgl_lahir' => 'date',
    ];

    public function getTempatDanTanggalLahirAttribute()
    {
        return $this->tempat_lahir . ', ' . $this->tgl_lahir->format('d F Y');
    }

    public function utusan(): BelongsTo
    {
        return $this->belongsTo(Utusan::class);
    }

    public function cabang(): BelongsTo
    {
        return $this->belongsTo(Cabang::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function tahun(): BelongsTo
    {
        return $this->belongsTo(Tahun::class);
    }

    public function nilaitartil(): HasOne
    {
        return $this->hasOne(NilaiTartil::class);
    }

    public function nilaianak(): HasOne
    {
        return $this->hasOne(NilaiAnak::class);
    }

    public function nilairemaja(): HasOne
    {
        return $this->hasOne(NilaiRemaja::class);
    }

    public function nilaidewasa(): HasOne
    {
        return $this->hasOne(NilaiDewasa::class);
    }

    public function nilaisatujuz(): HasOne
    {
        return $this->hasOne(NilaiSatuJuz::class);
    }

    public function nilailimajuz(): HasOne
    {
        return $this->hasOne(NilaiLimaJuz::class);
    }

    public function nilaisepuluhjuz(): HasOne
    {
        return $this->hasOne(NilaiSepuluhJuz::class);
    }

    public function nilaiduapuluhjuz(): HasOne
    {
        return $this->hasOne(NilaiDuapuluhJuz::class);
    }

    public function nilaitigapuluhjuz(): HasOne
    {
        return $this->hasOne(NilaiTigapuluhJuz::class);
    }

    public function nilaimfq(): HasOne
    {
        return $this->hasOne(NilaiMfq::class);
    }

    public function nilaimmq(): HasOne
    {
        return $this->hasOne(NilaiMmq::class);
    }

    public function nilaimsq(): HasOne
    {
        return $this->hasOne(NilaiMsq::class);
    }

    public function nilaimushaf(): HasOne
    {
        return $this->hasOne(NilaiMushaf::class);
    }

    public function nilainaskah(): HasOne
    {
        return $this->hasOne(NilaiNaskah::class);
    }

    public function grup(): BelongsTo
    {
        return $this->belongsTo(Grup::class);
    }
}
