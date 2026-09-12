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
        Schema::create('pesertas', function (Blueprint $table) {
            $table->id();
            $table->char('no_peserta')->nullable();
            $table->string('nama');
            $table->string('jenis_kelamin');
            $table->string('nik');
            $table->string('tempat_lahir');
            $table->date('tgl_lahir');
            $table->string('alamat_ktp');
            $table->string('alamat_domisili')->nullable();
            $table->foreignId('utusan_id')->constrained(
                table: 'utusans',
                indexName: 'pesertas_utusan_id'
            )->cascadeOnDelete();
            $table->foreignId('cabang_id')->constrained(
                table: 'cabangs',
                indexName: 'pesertas_cabang_id'
            )->cascadeOnDelete();
            $table->string('kk_ktp')->nullable();
            // $table->string('akta');
            // $table->string('ijazah');
            // $table->string('piagam');
            $table->string('pasfoto')->nullable();
            $table->boolean('is_verified')->default(false);
            $table->foreignId('user_id')->nullable()->constrained(
                table: 'users',
                indexName: 'pesertas_user_id'
            );
            $table->foreignId('tahun_id')->nullable()->constrained(
                table: 'tahuns',
                indexName: 'pesertas_tahun_id'
            );
            $table->foreignId('grup_id')->nullable()->constrained(
                table: 'grups',
                indexName: 'pesertas_grup_id'
            );
            $table->string('token', 64)->unique()->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pesertas');
    }
};
