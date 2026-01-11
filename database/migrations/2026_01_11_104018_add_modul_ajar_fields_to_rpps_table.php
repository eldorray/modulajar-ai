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
            $table->string('kelas')->nullable()->after('fase');
            $table->string('semester')->nullable()->after('kelas');
            $table->string('target_peserta_didik')->nullable()->after('semester');
            $table->integer('jumlah_pertemuan')->default(1)->after('alokasi_waktu');
            $table->text('kompetensi_awal')->nullable()->after('jumlah_pertemuan');
            $table->text('kata_kunci')->nullable()->after('kompetensi_awal');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('rpps', function (Blueprint $table) {
            $table->dropColumn([
                'kelas',
                'semester',
                'target_peserta_didik',
                'jumlah_pertemuan',
                'kompetensi_awal',
                'kata_kunci',
            ]);
        });
    }
};
