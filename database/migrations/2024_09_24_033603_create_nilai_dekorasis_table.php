<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('nilai_dekorasis', function (Blueprint $table) {
            $table->id();
            $table->float('kebenaran_kaidah_khath')->nullable();
            $table->float('bobot_kebenaran_kaidah_khath')->nullable();
            $table->float('keindahan_khath')->nullable();
            $table->float('bobot_keindahan_khath')->nullable();
            $table->float('keindahan_hiasan_dan_lukisan')->nullable();
            $table->float('bobot_keindahan_hiasan_dan_lukisan')->nullable();
            $table->float('total')->nullable();
            $table->float('bobot_total')->nullable();
            $table->float('final_bobot')->nullable();
            $table->foreignId('peserta_id')->constrained(
                table: 'pesertas',
                indexName: 'mkq_peserta_id'
            );
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('nilai_dekorasis');
    }
};
