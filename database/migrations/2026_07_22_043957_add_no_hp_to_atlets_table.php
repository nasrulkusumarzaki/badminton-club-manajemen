<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('atlets', function (Blueprint $table) {
            // nullable dulu biar aman kalau sudah ada data lama tanpa no_hp
            $table->string('no_hp', 20)->nullable()->after('umur');
        });
    }

    public function down(): void
    {
        Schema::table('atlets', function (Blueprint $table) {
            $table->dropColumn('no_hp');
        });
    }
};