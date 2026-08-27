<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Password sementara hasil reset admin. Disimpan terenkripsi (bukan plaintext,
     * bukan pengganti hash login) dan dihapus begitu user mengganti passwordnya.
     */
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->text('temp_password')->nullable()->after('password');
            $table->timestamp('temp_password_at')->nullable()->after('temp_password');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['temp_password', 'temp_password_at']);
        });
    }
};
