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
        Schema::create('nilai_satu_juzs', function (Blueprint $table) {
            $table->id();
            $table->float('til_tajwid')->nullable();
            $table->float('bobot_til_tajwid')->nullable();
            $table->float('til_lagu')->nullable();
            $table->float('bobot_til_lagu')->nullable();
            $table->float('til_suara')->nullable();
            $table->float('bobot_til_suara')->nullable();
            $table->float('til_fashahah')->nullable();
            $table->float('bobot_til_fashahah')->nullable();
            $table->float('tah_tahfizh')->nullable();
            $table->float('bobot_tah_tahfizh')->nullable();
            $table->float('tah_tajwid')->nullable();
            $table->float('bobot_tah_tajwid')->nullable();
            $table->float('tah_fashahah')->nullable();
            $table->float('bobot_tah_fashahah')->nullable();
            $table->float('total_tilawah')->nullable();
            $table->float('bobot_tilawah')->nullable();
            $table->float('bobot_total_tahfizh')->nullable();
            $table->float('total_tahfizh')->nullable();
            $table->float('bobot_tahfizh')->nullable();
            $table->float('total')->nullable();
            $table->float('bobot_total')->nullable();
            $table->float('final_bobot')->nullable();
            $table->foreignId('peserta_id')->constrained(
                table: 'pesertas',
                indexName: 'satu_juz_peserta_id'
            );
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('nilai_satu_juzs');
    }
};
