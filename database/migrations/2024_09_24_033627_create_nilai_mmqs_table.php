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
        Schema::create('nilai_mmqs', function (Blueprint $table) {
            $table->id();
            $table->float('bobot_materi')->nullable();
            $table->float('bobot_bobot_materi')->nullable();
            $table->float('kaidah_dan_gaya_bahasa')->nullable();
            $table->float('bobot_kaidah_dan_gaya_bahasa')->nullable();
            $table->float('logika_dan_organisasi_pesan')->nullable();
            $table->float('bobot_logika_dan_organisasi_pesan')->nullable();
            $table->float('presentasi')->nullable();
            $table->float('total')->nullable();
            $table->float('bobot_total')->nullable();
            $table->float('final_bobot')->nullable();
            $table->foreignId('peserta_id')->constrained(
                table: 'pesertas',
                indexName: 'mmq_peserta_id'
            );
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('nilai_mmqs');
    }
};
