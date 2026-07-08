<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('rpps', function (Blueprint $table) {
            // Tema warna dokumen (lihat config/rpp_themes.php). Persisted karena
            // dibaca saat render PDF/Word, bukan hanya saat generate.
            $table->string('tema', 20)->default('merah')->after('kurikulum');
        });
    }

    public function down(): void
    {
        Schema::table('rpps', function (Blueprint $table) {
            $table->dropColumn('tema');
        });
    }
};
