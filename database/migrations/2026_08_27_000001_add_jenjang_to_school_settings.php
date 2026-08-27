<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('school_settings', function (Blueprint $table) {
            $table->string('jenjang', 10)->default('MI')->after('id');
        });

        // Baris lama = profil MI
        DB::table('school_settings')->update(['jenjang' => 'MI']);

        Schema::table('rpps', function (Blueprint $table) {
            $table->string('jenjang', 10)->nullable()->after('user_id');
        });
    }

    public function down(): void
    {
        Schema::table('school_settings', function (Blueprint $table) {
            $table->dropColumn('jenjang');
        });

        Schema::table('rpps', function (Blueprint $table) {
            $table->dropColumn('jenjang');
        });
    }
};
