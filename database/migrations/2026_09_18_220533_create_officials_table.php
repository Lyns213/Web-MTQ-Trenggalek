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
        Schema::create('officials', function (Blueprint $table) {
            $table->id();
            $table->string('role', 30)->index(); // dewan_hakim, panitera, panitia
            $table->string('nama');
            $table->string('jabatan')->nullable();
            $table->string('foto')->nullable();
            $table->foreignId('tahun_id')->nullable()->constrained('tahuns')->nullOnDelete();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('officials');
    }
};
