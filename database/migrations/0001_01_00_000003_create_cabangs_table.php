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
        Schema::create('cabangs', function (Blueprint $table) {
            $table->id();
            $table->string('nama_cabang');
            $table->string('gender_cabang');
            $table->string('batas_umur');
            $table->date('per_tanggal');
            $table->integer('kuota');
            $table->time('timer')->nullable();
            // $table->foreignId('tahun_id')->nullable()->constrained(
            //     table: 'tahuns',
            //     indexName: 'cabangs_tahun_id'
            // );
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cabangs');
    }
};
