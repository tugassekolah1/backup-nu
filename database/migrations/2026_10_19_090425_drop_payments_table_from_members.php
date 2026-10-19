<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Fitur pembayaran pendaftaran anggota dihapus.
 *
 * Pendaftaran kini hanya menunggu verifikasi admin (kolom `registration_status`),
 * tanpa tahap pembayaran online. Tabel `payments` dan kolom `payment_status`
 * tidak lagi dipakai sehingga dibuang dari skema.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::dropIfExists('payments');

        Schema::table('nu_members', function (Blueprint $table) {
            $table->dropColumn('payment_status');
        });
    }

    public function down(): void
    {
        Schema::table('nu_members', function (Blueprint $table) {
            $table->enum('payment_status', ['unpaid', 'paid'])->default('unpaid');
        });

        Schema::create('payments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('nu_member_id')->constrained()->onDelete('cascade');
            $table->string('transaction_code')->unique();
            $table->decimal('amount', 10, 2);
            $table->string('payment_proof')->nullable()->after('amount');
            $table->enum('payment_status', ['unpaid', 'paid'])->default('unpaid');
            $table->timestamps();
        });
    }
};
