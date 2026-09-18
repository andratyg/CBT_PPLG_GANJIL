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

        Schema::create('jurnal_mengajar', function (Blueprint $table) {
            $table->integer('id')->primary()->autoIncrement();
            $table->integer('guru_id')->nullable();
            $table->foreign('guru_id')->references('id')->on('guru');
            $table->integer('pertemuan_id')->nullable();
            $table->foreign('pertemuan_id')->references('id')->on('pertemuan');
            $table->date('tanggal')->nullable();
            $table->integer('pertemuan_ke')->nullable();
            $table->string('topik', 200)->nullable();
            $table->text('uraian_kegiatan')->nullable();
            $table->text('hambatan')->nullable();
        });

        Schema::enableForeignKeyConstraints();
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('jurnal_mengajar');
    }
};
