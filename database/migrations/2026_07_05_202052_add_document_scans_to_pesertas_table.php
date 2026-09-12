<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('pesertas', function (Blueprint $table) {
            $table->string('scan_surat_mandat')->nullable()->after('pasfoto');
            $table->string('scan_kk')->nullable()->after('scan_surat_mandat');
            $table->string('scan_ijazah')->nullable()->after('scan_kk');
            $table->string('scan_akta_kelahiran')->nullable()->after('scan_ijazah');
        });
    }

    public function down(): void
    {
        Schema::table('pesertas', function (Blueprint $table) {
            $table->dropColumn(['scan_surat_mandat', 'scan_kk', 'scan_ijazah', 'scan_akta_kelahiran']);
        });
    }
};
