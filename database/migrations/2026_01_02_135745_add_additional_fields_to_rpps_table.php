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
        Schema::table('rpps', function (Blueprint $table) {
            $table->string('kepala_sekolah')->nullable()->after('nama_guru');
            $table->string('nip_kepala_sekolah')->nullable()->after('kepala_sekolah');
            $table->string('kota')->nullable()->after('nip_kepala_sekolah');
            $table->date('tanggal')->nullable()->after('kota');
            $table->string('jenis_asesmen')->nullable()->after('model_pembelajaran');
            $table->string('kurikulum')->default('Kurikulum Merdeka Belajar')->after('jenis_asesmen');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('rpps', function (Blueprint $table) {
            $table->dropColumn([
                'kepala_sekolah',
                'nip_kepala_sekolah',
                'kota',
                'tanggal',
                'jenis_asesmen',
                'kurikulum',
            ]);
        });
    }
};
