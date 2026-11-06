<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Kolom kata sandi untuk login mandiri anggota (guard "member").
     * Nullable karena data lama dan anggota buatan admin belum punya
     * kata sandi — mereka dapat membuatnya lewat halaman lupa password.
     */
    public function up(): void
    {
        Schema::table('nu_members', function (Blueprint $table) {
            $table->string('password')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('nu_members', function (Blueprint $table) {
            $table->dropColumn('password');
        });
    }
};
