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

        Schema::create('pengumpulan_tugas', function (Blueprint $table) {
            $table->integer('id')->primary()->autoIncrement();
            $table->integer('tugas_id')->nullable();
            $table->foreign('tugas_id')->references('id')->on('tugas');
            $table->integer('siswa_id')->nullable();
            $table->foreign('siswa_id')->references('id')->on('siswa');
            $table->string('path_file', 255)->nullable();
            $table->integer('skor')->nullable();
            $table->text('catatan')->nullable();
            $table->string('status', 20)->nullable();
            $table->dateTime('waktu_kumpul')->nullable();
        });

        Schema::enableForeignKeyConstraints();
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pengumpulan_tugas');
    }
};
