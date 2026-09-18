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

        Schema::create('kuis', function (Blueprint $table) {
            $table->integer('id')->primary()->autoIncrement();
            $table->string('judul', 200)->nullable();
            $table->string('token', 20)->nullable();
            $table->integer('durasi_menit')->nullable();
            $table->dateTime('waktu_mulai')->nullable();
        });

        Schema::enableForeignKeyConstraints();
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('kuis');
    }
};
