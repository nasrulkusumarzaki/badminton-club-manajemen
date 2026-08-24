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
        Schema::create('atlets', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->string('nama');
            $table->unsignedTinyInteger('umur');
            $table->string('jenis_kelamin');
            $table->enum('level', ['pemula', 'beginner', 'senior']);
            $table->string('foto')->nullable(); // path file foto di storage
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('atlets');
    }
};
