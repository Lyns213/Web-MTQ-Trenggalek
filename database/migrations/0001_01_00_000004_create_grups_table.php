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
        Schema::create('grups', function (Blueprint $table) {
            $table->id();
            $table->string('nama');
            $table->foreignId('tahun_id')->constrained(
                table: 'tahuns',
                indexName: 'grups_tahun_id'
            );
            $table->foreignId('utusan_id')->constrained(
                table: 'utusans',
                indexName: 'grups_utusan_id'
            );
            $table->string('jenis_kelamin');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('grups');
    }
};
