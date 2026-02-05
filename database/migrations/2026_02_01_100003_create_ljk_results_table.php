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
        Schema::create('ljk_results', function (Blueprint $table) {
            $table->id();
            $table->foreignId('ljk_answer_key_id')->constrained()->onDelete('cascade');
            $table->foreignId('user_id')->constrained()->onDelete('cascade'); // teacher who graded
            $table->string('nama_peserta')->nullable();
            $table->string('nomor_peserta')->nullable();
            $table->string('kelas')->nullable();
            $table->json('jawaban_siswa'); // array of student answers ['A', 'B', 'C', ...]
            $table->integer('jumlah_benar')->default(0);
            $table->integer('jumlah_salah')->default(0);
            $table->integer('jumlah_kosong')->default(0);
            $table->decimal('skor', 5, 2)->default(0);
            $table->string('scan_image')->nullable(); // path to scanned image
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ljk_results');
    }
};
