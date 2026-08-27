<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Modul STS & LJK dihapus. Data lama sudah di-backup manual sebelum drop.
     */
    public function up(): void
    {
        Schema::dropIfExists('ljk_results');
        Schema::dropIfExists('ljk_answer_keys');
        Schema::dropIfExists('ljk_templates');
        Schema::dropIfExists('sts');
    }

    public function down(): void
    {
        // ponytail: irreversible — modul sudah dihapus, restore dari dump SQL kalau perlu.
    }
};
