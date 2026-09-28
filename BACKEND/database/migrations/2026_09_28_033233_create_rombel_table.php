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
        Schema::create('rombel', function (Blueprint $table) {
            $table->id();
            $table->string('nama', 50)->unique();
            $table->timestamps();
        });

        // Seed distinct existing rombels from siswa table
        $existing = \Illuminate\Support\Facades\DB::table('siswa')
            ->whereNotNull('rombel')
            ->where('rombel', '!=', '')
            ->distinct()
            ->pluck('rombel');

        foreach ($existing as $r) {
            \Illuminate\Support\Facades\DB::table('rombel')->insertOrIgnore([
                'nama' => trim($r),
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('rombel');
    }
};
