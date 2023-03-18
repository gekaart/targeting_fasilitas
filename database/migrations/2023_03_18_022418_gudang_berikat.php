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
        Schema::create('gudang_berikat', function (Blueprint $table) {
            $table->id();
            $table->string('npwp_pengusaha');
            $table->string('nama_pengusaha');
            $table->integer('komoditi');
            $table->integer('pemasok');
            $table->integer('tonase');
            $table->integer('cif');
            $table->integer('skors');
            $table->string('status');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('gudang_berikat');
    }
};
