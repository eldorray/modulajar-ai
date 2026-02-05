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
        Schema::create('ljk_answer_keys', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('ljk_template_id')->nullable()->constrained()->onDelete('set null');
            $table->string('nama'); // name of this answer key
            $table->string('mata_pelajaran');
            $table->string('kelas')->nullable();
            $table->integer('jumlah_soal')->default(40);
            $table->integer('jumlah_pilihan')->default(4);
            $table->json('kunci_jawaban'); // array of correct answers ['A', 'B', 'C', ...]
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ljk_answer_keys');
    }
};
