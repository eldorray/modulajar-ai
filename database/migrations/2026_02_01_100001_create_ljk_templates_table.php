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
        Schema::create('ljk_templates', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('nama_template');
            $table->string('kop_image')->nullable(); // path to uploaded header image
            $table->string('jenis_ujian')->default('STS'); // STS, SAS, UTS, UAS, etc
            $table->string('tahun_ajaran')->nullable();
            $table->integer('jumlah_soal')->default(40);
            $table->integer('jumlah_pilihan')->default(4); // 4 = A,B,C,D or 5 = A,B,C,D,E
            $table->json('mata_pelajaran_list')->nullable(); // array of subjects
            $table->boolean('show_essay_lines')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ljk_templates');
    }
};
