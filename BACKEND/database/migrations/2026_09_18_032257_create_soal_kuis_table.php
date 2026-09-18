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

        Schema::create('soal_kuis', function (Blueprint $table) {
            $table->integer('id')->primary()->autoIncrement();
            $table->integer('kuis_id')->nullable();
            $table->foreign('kuis_id')->references('id')->on('kuis');
            $table->string('jenis_soal', 20)->nullable();
            $table->text('pertanyaan')->nullable();
            $table->string('pilihan_a', 255)->nullable();
            $table->string('pilihan_b', 255)->nullable();
            $table->string('pilihan_c', 255)->nullable();
            $table->string('pilihan_d', 255)->nullable();
            $table->string('kunci_jawaban', 255)->nullable();
        });

        Schema::enableForeignKeyConstraints();
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('soal_kuis');
    }
};
