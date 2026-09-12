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
        Schema::create('nilai_msqs', function (Blueprint $table) {
            $table->id();
            $table->float('terjemahan_dan_materi')->nullable();
            $table->float('bobot_terjemahan_dan_materi')->nullable();
            $table->float('penghayatan_dan_retorika')->nullable();
            $table->float('bobot_penghayatan_dan_retorika')->nullable();
            $table->float('tilawah')->nullable();
            $table->float('bobot_tilawah')->nullable();
            $table->float('total')->nullable();
            $table->float('bobot_total')->nullable();
            $table->float('final_bobot')->nullable();
            $table->foreignId('peserta_id')->constrained(
                table: 'pesertas',
                indexName: 'msq_peserta_id'
            );
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('nilai_msqs');
    }
};
