<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Desain (skin) dokumen. NULL berarti dokumen dibuat sebelum fitur ini ada;
     * RppDocumentStyle memetakannya ke desain default, jadi tidak perlu backfill.
     */
    public function up(): void
    {
        Schema::table('rpps', function (Blueprint $table) {
            $table->string('desain')->nullable()->after('tema');
        });
    }

    public function down(): void
    {
        Schema::table('rpps', function (Blueprint $table) {
            $table->dropColumn('desain');
        });
    }
};
