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
        Schema::create('nilai_duapuluh_juzs', function (Blueprint $table) {
            $table->id();
            $table->float('tahfizh')->nullable();
            $table->float('bobot_tahfizh')->nullable();
            $table->float('tajwid')->nullable();
            $table->float('bobot_tajwid')->nullable();
            $table->float('fashahah')->nullable();
            $table->float('bobot_fashahah')->nullable();
            $table->float('total')->nullable();
            $table->float('bobot_total')->nullable();
            $table->float('final_bobot')->nullable();
            $table->foreignId('peserta_id')->constrained(
                table: 'pesertas',
                indexName: 'duapuluh_juz_peserta_id'
            );
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('nilai_duapuluh_juzs');
    }
};
