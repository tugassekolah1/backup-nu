<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class DatabaseSeeder extends Seeder
{
    /**
     * Membuat akun admin pertama.
     *
     * Password TIDAK lagi hardcode. Urutannya:
     * 1. `SEEDER_ADMIN_PASSWORD` dari .env (dipakai saat instalasi/deploy), atau
     * 2. password acak 16 karakter yang dicetak ke terminal sekali saja.
     */
    public function run(): void
    {
        $email = env('SEEDER_ADMIN_EMAIL', 'admin@gmail.com');
        $password = env('SEEDER_ADMIN_PASSWORD');

        if (blank($password)) {
            $password = Str::password(16);

            $this->command?->warn('Password admin dibuat otomatis. Catat sekarang, dan ganti setelah login pertama:');
            $this->command?->line("  Email    : {$email}");
            $this->command?->line('  Password : '.$password);
        }

        User::updateOrCreate(
            ['email' => $email],
            [
                'name' => env('SEEDER_ADMIN_NAME', 'Admin'),
                'password' => Hash::make($password),
                'is_admin' => true,
            ],
        );
    }
}
