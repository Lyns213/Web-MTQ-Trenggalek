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
        Schema::create('nilai_mfqs', function (Blueprint $table) {
            $table->id();
            $table->float('total')->nullable();
            $table->foreignId('grup_id')->constrained(
                table: 'grups',
                indexName: 'mfqs_grup_id'
            );
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('nilai_mfqs');
    }
};
