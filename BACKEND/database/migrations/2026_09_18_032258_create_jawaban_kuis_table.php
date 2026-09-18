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
        Schema::disableForeignKeyConstraints();

        Schema::create('jawaban_kuis', function (Blueprint $table) {
            $table->integer('id')->primary()->autoIncrement();
            $table->integer('kuis_id')->nullable();
            $table->foreign('kuis_id')->references('id')->on('kuis');
            $table->integer('siswa_id')->nullable();
            $table->foreign('siswa_id')->references('id')->on('siswa');
            $table->integer('soal_id')->nullable();
            $table->foreign('soal_id')->references('id')->on('soal_kuis');
            $table->text('jawaban')->nullable();
            $table->integer('skor')->nullable();
        });

        Schema::enableForeignKeyConstraints();
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('jawaban_kuis');
    }
};
