<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('pesertas', function (Blueprint $table) {
            $table->boolean('check_scan_ktp')->default(false)->after('scan_ktp');
            $table->boolean('check_scan_surat_mandat')->default(false)->after('scan_surat_mandat');
            $table->boolean('check_scan_kk')->default(false)->after('scan_kk');
            $table->boolean('check_scan_ijazah')->default(false)->after('scan_ijazah');
            $table->boolean('check_scan_akta_kelahiran')->default(false)->after('scan_akta_kelahiran');
            $table->boolean('check_scan_biodata')->default(false)->after('scan_biodata');
            $table->boolean('check_scan_surat_domisili')->default(false)->after('scan_surat_keterangan_domisili');
        });
    }

    public function down(): void
    {
        Schema::table('pesertas', function (Blueprint $table) {
            $table->dropColumn([
                'check_scan_ktp',
                'check_scan_surat_mandat',
                'check_scan_kk',
                'check_scan_ijazah',
                'check_scan_akta_kelahiran',
                'check_scan_biodata',
                'check_scan_surat_domisili',
            ]);
        });
    }
};
