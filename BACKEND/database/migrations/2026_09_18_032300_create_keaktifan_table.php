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

        Schema::create('keaktifan', function (Blueprint $table) {
            $table->integer('id')->primary()->autoIncrement();
            $table->integer('siswa_id')->nullable();
            $table->foreign('siswa_id')->references('id')->on('siswa');
            $table->integer('pertemuan_id')->nullable();
            $table->foreign('pertemuan_id')->references('id')->on('pertemuan');
            $table->integer('poin')->nullable();
            $table->string('keterangan', 255)->nullable();
            $table->date('tanggal')->nullable();
        });

        Schema::enableForeignKeyConstraints();
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('keaktifan');
    }
};
