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
        Schema::create('nilai_kontemporers', function (Blueprint $table) {
            $table->id();
            $table->float('unsur_kaligrafi')->nullable();
            $table->float('bobot_unsur_kaligrafi')->nullable();
            $table->float('unsur_seni_rupa')->nullable();
            $table->float('bobot_unsur_seni_rupa')->nullable();
            $table->float('sentuhan_akhir')->nullable();
            $table->float('bobot_sentuhan_akhir')->nullable();
            $table->float('total')->nullable();
            $table->float('bobot_total')->nullable();
            $table->float('final_bobot')->nullable();
            $table->foreignId('peserta_id')->constrained(
                table: 'pesertas',
                indexName: 'kontemporer_peserta_id'
            );
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('nilai_kontemporers');
    }
};
