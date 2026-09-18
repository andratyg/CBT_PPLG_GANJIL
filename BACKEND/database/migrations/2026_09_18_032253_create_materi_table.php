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

        Schema::create('materi', function (Blueprint $table) {
            $table->integer('id')->primary()->autoIncrement();
            $table->integer('bab_id')->nullable();
            $table->foreign('bab_id')->references('id')->on('bab');
            $table->integer('pertemuan_id')->nullable();
            $table->foreign('pertemuan_id')->references('id')->on('pertemuan');
            $table->string('judul', 200)->nullable();
            $table->string('jenis_file', 20)->nullable();
            $table->string('path_file', 255)->nullable();
            $table->string('url_eksternal', 255)->nullable();
        });

        Schema::enableForeignKeyConstraints();
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('materi');
    }
};
