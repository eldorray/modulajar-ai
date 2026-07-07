<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * `topik` was VARCHAR(255) but the store() validation allows max:1000,
     * so a topik between 256-1000 chars passed validation then failed the
     * insert with "Data too long for column 'topik'". Widen to TEXT to match
     * its sibling free-text columns (kompetensi_awal, kata_kunci).
     */
    public function up(): void
    {
        Schema::table('rpps', function (Blueprint $table) {
            $table->text('topik')->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('rpps', function (Blueprint $table) {
            $table->string('topik')->change();
        });
    }
};
