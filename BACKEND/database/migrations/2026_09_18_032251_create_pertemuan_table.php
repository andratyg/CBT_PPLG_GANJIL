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

        Schema::create('pertemuan', function (Blueprint $table) {
            $table->integer('id')->primary()->autoIncrement();
            $table->date('tanggal')->nullable();
            $table->integer('pertemuan_ke')->nullable();
            $table->integer('bab_id')->nullable();
            $table->foreign('bab_id')->references('id')->on('bab');
            $table->string('topik', 200)->nullable();
        });

        Schema::enableForeignKeyConstraints();
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pertemuan');
    }
};
