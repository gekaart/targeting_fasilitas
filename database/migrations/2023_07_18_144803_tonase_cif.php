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
        Schema::create('tonaseCIF', function (Blueprint $table) {
            $table->id();
            $table->string('npwp_pengusaha');
            $table->string('nama_pengusaha');
            $table->string('hs_code');
            $table->string('skor');
            $table->string('level');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tonaseCIF');
    }
};
