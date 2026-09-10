<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::table('school_settings')->whereIn('jenjang', ['MI', 'SD'])->update(['jenjang' => 'MI/SD']);
        DB::table('school_settings')->whereIn('jenjang', ['SMP', 'MTs'])->update(['jenjang' => 'SMP/MTs']);
        DB::table('school_settings')->whereIn('jenjang', ['SMA', 'SMK'])->update(['jenjang' => 'SMA/SMK']);

        DB::table('rpps')->whereIn('jenjang', ['MI', 'SD'])->update(['jenjang' => 'MI/SD']);
        DB::table('rpps')->whereIn('jenjang', ['SMP', 'MTs'])->update(['jenjang' => 'SMP/MTs']);
        DB::table('rpps')->whereIn('jenjang', ['SMA', 'SMK'])->update(['jenjang' => 'SMA/SMK']);
    }

    public function down(): void
    {
        DB::table('school_settings')->where('jenjang', 'MI/SD')->update(['jenjang' => 'MI']);
        DB::table('school_settings')->where('jenjang', 'SMP/MTs')->update(['jenjang' => 'SMP']);
        DB::table('school_settings')->where('jenjang', 'SMA/SMK')->update(['jenjang' => 'SMA']);

        DB::table('rpps')->where('jenjang', 'MI/SD')->update(['jenjang' => 'MI']);
        DB::table('rpps')->where('jenjang', 'SMP/MTs')->update(['jenjang' => 'SMP']);
        DB::table('rpps')->where('jenjang', 'SMA/SMK')->update(['jenjang' => 'SMA']);
    }
};
