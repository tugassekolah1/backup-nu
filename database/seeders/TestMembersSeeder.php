<?php

namespace Database\Seeders;

use App\Models\NuMember;
use App\Models\Pengurus;
use Illuminate\Database\Seeder;

class TestMembersSeeder extends Seeder
{
    public function run()
    {
        $banom_codes = [
            'pac_muslimat', 'pac_fatayat', 'pac_ansor', 'satkoryon_banser',
            'pac_ipnu', 'cbp_ipnu', 'pac_ippnu', 'kpp_ippnu',
            'pac_pencak_silat', 'pac_pargu', 'pac_isnu', 'pac_jqhnu',
            'pac_jatman', 'pac_ishari',
        ];

        $names_male = ['Ahmad', 'Budi', 'Candra', 'Dedi', 'Eko', 'Fajar', 'Gita', 'Hendra', 'Irawan', 'Jamal'];
        $names_female = ['Aida', 'Bella', 'Citra', 'Diana', 'Eka', 'Fatma', 'Gina', 'Hana', 'Ida', 'Jaka'];
        $surnames = ['Wijaya', 'Kusuma', 'Santoso', 'Rahman', 'Pratama', 'Handoko', 'Suprapto', 'Hermawan', 'Gunawan', 'Sasongko'];

        $phones = [];
        for ($i = 0; $i < 1000; $i++) {
            $phones[] = '08'.str_pad(rand(1, 999999999), 9, '0', STR_PAD_LEFT);
        }

        $this->command->info('Generating 1000 test members...');

        for ($i = 1; $i <= 1000; $i++) {
            $gender = rand(0, 1) ? 'L' : 'P';
            $name_first = $gender === 'L' ? $names_male[array_rand($names_male)] : $names_female[array_rand($names_female)];
            $name_last = $surnames[array_rand($surnames)];
            $full_name = $name_first.' '.$name_last;

            $nik = str_pad($i, 16, '0', STR_PAD_LEFT);
            $organisasi = rand(0, 1) ? $banom_codes[array_rand($banom_codes)] : null;
            $isApproved = rand(1, 100) <= 70;

            NuMember::create([
                'nik' => $nik,
                'full_name' => $full_name,
                'phone' => $phones[$i - 1],
                'gender' => $gender,
                'organisasi' => $organisasi,
                'label_organisasi' => $organisasi ? (Pengurus::BANOMS[$organisasi]['label'] ?? null) : null,
                'address' => "Jl. Test No. $i, Kota Test",
                'photo' => null,
                'status' => $isApproved ? 'active' : 'pending_payment',
                'registration_status' => $isApproved ? 'accepted' : 'pending',
                'member_card_no' => $isApproved ? $this->generateCardNumber() : null,
            ]);

            if ($i % 100 === 0) {
                $this->command->line("Generated $i members...");
            }
        }

        $this->command->info('✓ 1000 test members created successfully!');
    }

    private function generateCardNumber(): string
    {
        $year = now()->format('Y');
        $lastNumber = NuMember::where('member_card_no', 'like', "NU-{$year}-%")
            ->orderByDesc('member_card_no')
            ->value('member_card_no');

        $nextSequence = 1;
        if ($lastNumber) {
            $lastSequence = (int) substr($lastNumber, -3);
            $nextSequence = $lastSequence + 1;
        }

        return sprintf('NU-%s-%03d', $year, $nextSequence);
    }
}
