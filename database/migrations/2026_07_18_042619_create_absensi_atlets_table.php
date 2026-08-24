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
        Schema::create('absensi_atlets', function (Blueprint $table) {
            $table->id();
            $table->date('tanggal');
            $table->foreignId('atlet_id')->constrained('atlets')->cascadeOnDelete();
            $table->enum('status', ['hadir', 'tidak']);
            $table->foreignId('dicatat_oleh')->constrained('users')->cascadeOnDelete();
            $table->timestamps();

            $table->unique(['tanggal', 'atlet_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('absensi_atlets');
    }
};
