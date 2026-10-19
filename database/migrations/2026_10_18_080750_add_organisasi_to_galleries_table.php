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
        Schema::table('galleries', function (Blueprint $table) {
            // Slug organisasi sesuai config('organisasi.organisasi').
            // Nullable: foto kegiatan umum yang tidak terikat satu banom.
            $table->string('organisasi', 80)->nullable()->after('judul')->index();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('galleries', function (Blueprint $table) {
            $table->dropIndex(['organisasi']);
            $table->dropColumn('organisasi');
        });
    }
};
