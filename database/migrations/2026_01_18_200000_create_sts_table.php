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
        Schema::create('sts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('mata_pelajaran');
            $table->string('kelas');
            $table->string('fase');
            $table->text('topik');
            $table->text('tujuan_pembelajaran')->nullable();
            $table->integer('jumlah_soal')->default(20);
            $table->integer('jumlah_pg')->default(10);
            $table->integer('jumlah_pg_kompleks')->default(3);
            $table->integer('jumlah_menjodohkan')->default(5);
            $table->integer('jumlah_uraian')->default(2);
            $table->json('content_result')->nullable();
            $table->enum('status', ['draft', 'processing', 'completed', 'failed'])->default('draft');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sts');
    }
};
