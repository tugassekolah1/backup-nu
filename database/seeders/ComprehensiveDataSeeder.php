<?php

namespace Database\Seeders;

use App\Models\Agenda;
use App\Models\Aspirasi;
use App\Models\Berita;
use App\Models\BeritaView;
use App\Models\Gallery;
use App\Models\Infaq;
use App\Models\Pengurus;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ComprehensiveDataSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->command->info('🚀 Starting Comprehensive Data Seeding...');
        $this->command->newLine();

        // Truncate existing data to avoid unique constraint issues
        $this->command->info('🗑️  Cleaning up existing data...');
        DB::table('beritas')->truncate();
        DB::table('agendas')->truncate();
        DB::table('galleries')->truncate();
        DB::table('infaqs')->truncate();
        DB::table('aspirasis')->truncate();
        DB::table('penguruses')->truncate();
        // Rekap harian pembaca harus ikut dibersihkan: barisnya menunjuk berita
        // yang ikut terhapus, dan kalau dibiarkan grafik tren akan membawa sisa
        // data berita lama.
        DB::table('berita_views')->truncate();
        $this->command->line('  ✅ Cleaned');
        $this->command->newLine();

        // 1. Seed Berita (500)
        $this->seedBerita();

        // Sebar total views berita ke rekap harian, supaya grafik tren 30 hari
        // langsung terisi pada instalasi baru (tanpa menunggu kunjungan asli).
        $dibuat = BeritaView::backfillDariTotalViews();
        $this->command->line("  ✅ {$dibuat} baris rekap harian pembaca dibuat untuk grafik tren");
        $this->command->newLine();

        // 2. Seed Agenda (500)
        $this->seedAgenda();

        // 3. Seed Gallery (100)
        $this->seedGallery();

        // 4. Seed Transaksi Infaq (1000 campuran)
        $this->seedTransaksi();

        // 5. Seed Aspirasi (400)
        $this->seedAspirasi();

        // 6. Seed Pengurus (80 campur semua organisasi)
        $this->seedPengurus();

        $this->command->newLine();
        $this->command->info('✅ All data seeded successfully!');
        $this->showSummary();
    }

    /**
     * Seed 500 Berita
     */
    private function seedBerita(): void
    {
        $this->command->info('📰 Seeding 500 Berita...');

        $titles = [
            'Peluncuran Program Dakwah Digital NU',
            'Pengajian Rutin Minggu di Masjid Ranting',
            'Kegiatan Sosial Pembagian Sembako',
            'Pertemuan Koordinasi Banom Kabupaten',
            'Pelatihan Kader Muda NU Berlanjut',
            'Kemitraan NU dengan Lembaga Pendidikan',
            'Acara Silaturahmi Pemimpin Organisasi',
            'Program Pemberdayaan Ekonomi UMKM',
            'Sosialisasi Nilai-Nilai NU kepada Generasi Muda',
            'Penghargaan untuk Tokoh Pengabdi Masyarakat',
        ];

        $contents = [
            'Organisasi Nahdlatul Ulama terus berinovasi dalam mengembangkan program dakwah yang relevan dengan era digital. Melalui platform media sosial dan website, pesan-pesan dakwah dapat menjangkau lebih banyak masyarakat.',
            'Pengajian rutin yang diadakan setiap minggu di masjid ranting menjadi ajang berkumpulnya umat untuk meningkatkan pemahaman agama. Kehadiran yang konsisten menunjukkan tingginya antusiasme masyarakat.',
            'Dalam rangka menjalankan amal jariyah, NU melakukan program pembagian sembako kepada keluarga yang membutuhkan di berbagai daerah.',
            'Pertemuan koordinasi dihadiri oleh semua pimpinan banom untuk membahas strategi pengembangan organisasi di tingkat kabupaten.',
            'Pelatihan kader muda NU merupakan investasi jangka panjang untuk mempersiapkan generasi penerus yang berkualitas dan berkarakter.',
            'Kerjasama dengan institusi pendidikan membuka peluang baru bagi pengembangan sumber daya manusia yang berkomitmen pada nilai-nilai NU.',
            'Acara silaturahmi memperkuat ikatan persaudaraan antar pemimpin organisasi dan menciptakan sinergi yang lebih baik.',
            'Program pemberdayaan ekonomi fokus pada peningkatan UMKM melalui pelatihan, pendampingan, dan akses pasar yang lebih luas.',
            'Sosialisasi nilai-nilai NU kepada generasi muda dilakukan melalui berbagai media dan pendekatan yang menarik dan relevan.',
            'Penghargaan diberikan kepada tokoh-tokoh yang telah berdedikasi dalam melayani masyarakat dan memperjuangkan nilai-nilai kemanusiaan.',
        ];

        for ($i = 1; $i <= 500; $i++) {
            Berita::factory()->create([
                'judul' => $titles[($i - 1) % count($titles)].' - Edisi '.$i,
                'jenis' => 'Berita',
                'slug' => 'berita-'.str_pad($i, 3, '0', STR_PAD_LEFT),
                'isi' => $contents[($i - 1) % count($contents)],
                'gambar' => null,
                'user_id' => 1,
                'status' => true,
                'views' => rand(10, 500),
                'created_at' => now()->subDays(rand(0, 90)),
                'updated_at' => now()->subDays(rand(0, 90)),
            ]);

            if ($i % 100 === 0) {
                $this->command->line("  Generated $i/500 berita...");
            }
        }

        $this->command->info('  ✅ 500 berita created');
    }

    /**
     * Seed 500 Agenda
     */
    private function seedAgenda(): void
    {
        $this->command->info('📅 Seeding 500 Agenda...');

        $eventTypes = [
            'Pengajian',
            'Pertemuan',
            'Pelatihan',
            'Acara Sosial',
            'Silaturahmi',
            'Musyawarah',
            'Pemberdayaan',
            'Kampanye',
            'Seminar',
            'Workshop',
        ];

        $locations = [
            'Masjid Ranting Al-Ikhlas',
            'Kantor PAC NU',
            'Gedung Pertemuan Ranting',
            'Aula Kantor Cabang',
            'Lapangan Ranting',
            'Sekolah Dasar Negeri',
            'Madrasah Aliyah',
            'Balai Desa',
            'Kantor Camat',
            'Rumah Ranting',
        ];

        for ($i = 1; $i <= 500; $i++) {
            $eventDate = now()->addDays(rand(1, 180));

            Agenda::create([
                'title' => $eventTypes[($i - 1) % count($eventTypes)].' #'.$i,
                'category' => $eventTypes[($i - 1) % count($eventTypes)],
                'event_date' => $eventDate->toDateString(),
                'event_time' => rand(7, 17).':00',
                'location' => $locations[($i - 1) % count($locations)],
                'description' => 'Kegiatan '.$eventTypes[($i - 1) % count($eventTypes)].' yang diadakan untuk meningkatkan silaturahmi dan koordinasi di antara anggota organisasi.',
                'created_at' => now()->subDays(rand(0, 60)),
                'updated_at' => now()->subDays(rand(0, 60)),
            ]);

            if ($i % 100 === 0) {
                $this->command->line("  Generated $i/500 agenda...");
            }
        }

        $this->command->info('  ✅ 500 agenda created');
    }

    /**
     * Seed 100 Gallery
     */
    private function seedGallery(): void
    {
        $this->command->info('🖼️  Seeding 100 Foto Galeri...');

        $organisasi = Gallery::daftarOrganisasi();

        for ($i = 1; $i <= 100; $i++) {
            $imageUrl = 'https://picsum.photos/400/300?random='.($i + rand(1000, 9999));

            // Sebagian foto sengaja dibiarkan tanpa organisasi agar tersedia
            // contoh foto kegiatan umum yang tidak terikat satu banom.
            $org = $i % 4 === 0 ? null : ($organisasi[array_rand($organisasi)] ?? null);

            $namaOrg = $org['nama'] ?? 'Nahdlatul Ulama Ranting Banjaranyar';
            $singkat = $org['singkatan'] ?? 'Kegiatan Umum';

            Gallery::create([
                'judul' => $singkat.' — Dokumentasi Kegiatan #'.str_pad($i, 3, '0', STR_PAD_LEFT),
                'organisasi' => $org['slug'] ?? null,
                'deskripsi' => "Dokumentasi kegiatan {$namaOrg} yang menampilkan momen berharga antara anggota dan komunitas.",
                'foto' => $imageUrl,
                'urutan' => $i,
                'created_at' => now()->subDays(rand(0, 90)),
                'updated_at' => now()->subDays(rand(0, 90)),
            ]);

            if ($i % 25 === 0) {
                $this->command->line("  Generated $i/100 gallery images...");
            }
        }

        $this->command->info('  ✅ 100 gallery images created');
    }

    /**
     * Seed 1000 Transaksi Infaq (Campuran Masuk & Keluar)
     */
    private function seedTransaksi(): void
    {
        $this->command->info('💰 Seeding 1000 Transaksi Infaq (Masuk & Keluar)...');

        $kategoriMasuk = ['Infaq Pribadi', 'Infaq Kolektif', 'Zakat', 'Sedekah', 'Wakaf'];
        $kategoriKeluar = ['Bantuan Sosial', 'Beasiswa', 'Renovasi Masjid', 'Operasional', 'Program Dakwah'];
        $metode = ['transfer_bank', 'qris', 'tunai'];

        for ($i = 1; $i <= 1000; $i++) {
            $arah = $i % 2 === 0 ? 'masuk' : 'keluar';
            $isMasuk = $arah === 'masuk';

            Infaq::create([
                'kode_transaksi' => 'TRX-'.strtoupper(uniqid()),
                'nama_donatur' => 'Donatur #'.str_pad($i, 4, '0', STR_PAD_LEFT),
                'no_hp' => '08'.rand(10000000, 99999999),
                'nominal' => collect([50000, 100000, 250000, 500000, 1000000])->random(),
                'metode_pembayaran' => $metode[array_rand($metode)],
                'status' => collect(['pending', 'lunas', 'dibatalkan'])->random(),
                'arah' => $arah,
                'kategori' => $isMasuk
                    ? $kategoriMasuk[($i - 1) % count($kategoriMasuk)]
                    : $kategoriKeluar[($i - 1) % count($kategoriKeluar)],
                'penanggung_jawab' => 'Admin #'.rand(1, 5),
                'catatan' => ($isMasuk ? 'Penerimaan' : 'Pengeluaran').' infaq/dana organisasi',
                'paid_at' => rand(0, 1) ? now()->subDays(rand(0, 180)) : null,
                'created_at' => now()->subDays(rand(0, 180)),
                'updated_at' => now()->subDays(rand(0, 180)),
            ]);

            if ($i % 250 === 0) {
                $this->command->line("  Generated $i/1000 transaksi...");
            }
        }

        $this->command->info('  ✅ 1000 transaksi infaq created (500 masuk, 500 keluar)');
    }

    /**
     * Seed 400 Aspirasi
     */
    private function seedAspirasi(): void
    {
        $this->command->info('💬 Seeding 400 Aspirasi...');

        $categories = ['Saran', 'Kritik', 'Pengaduan', 'Pertanyaan', 'Usulan'];
        $topics = [
            'Program Kegiatan NU',
            'Manajemen Organisasi',
            'Pemberdayaan Anggota',
            'Dakwah dan Pendidikan',
            'Hubungan dengan Masyarakat',
            'Kesejahteraan Anggota',
            'Fasilitas Organisasi',
            'Komunikasi Internal',
            'Transparansi Keuangan',
            'Kemitraan Strategis',
        ];

        $contents = [
            'Menurut saya, program yang dijalankan sudah bagus namun perlu ditingkatkan transparansi dalam penggunaan dana.',
            'Saya melihat ada potensi untuk mengembangkan program pemberdayaan ekonomi anggota dengan skala yang lebih besar.',
            'Silakan pertimbangkan untuk meningkatkan fasilitas rapat dan pelatihan di kantor organisasi.',
            'Bagaimana cara anggota bisa berkontribusi lebih aktif dalam keputusan strategis organisasi?',
            'Kami mengusulkan agar program dakwah lebih disesuaikan dengan kebutuhan generasi muda.',
            'Perlu ada sistem komunikasi yang lebih baik antara tingkat pusat, cabang, dan ranting.',
            'Saya ingin mengetahui bagaimana mekanisme pemilihan pemimpin organisasi yang transparan.',
            'Ada kekhawatiran tentang keterlibatan anggota perempuan dalam struktur kepemimpinan.',
            'Usul untuk membuat platform digital yang lebih interaktif bagi seluruh anggota.',
            'Bagaimana rencana pengembangan organisasi untuk 5 tahun ke depan?',
        ];

        for ($i = 1; $i <= 400; $i++) {
            $kode = 'ASP-'.strtoupper(bin2hex(random_bytes(4)));

            Aspirasi::create([
                'kode' => $kode,
                'nama' => 'Anggota #'.str_pad($i, 4, '0', STR_PAD_LEFT),
                'email' => 'anggota'.$i.'@nu.org',
                'no_hp' => '08'.rand(10000000, 99999999),
                'kategori' => $categories[($i - 1) % count($categories)],
                'isi' => $contents[($i - 1) % count($contents)],
                'status' => collect(['baru', 'diproses', 'selesai', 'ditolak'])->random(),
                'tanggapan' => rand(0, 1) ? 'Terima kasih atas masukan Anda. Tim kami akan segera menindaklanjutinya.' : null,
                'tanggapan_at' => rand(0, 1) ? now()->subDays(rand(0, 60)) : null,
                'created_at' => now()->subDays(rand(0, 120)),
                'updated_at' => now()->subDays(rand(0, 120)),
            ]);

            if ($i % 100 === 0) {
                $this->command->line("  Generated $i/400 aspirasi...");
            }
        }

        $this->command->info('  ✅ 400 aspirasi created');
    }

    /**
     * Seed 80 Pengurus (Campur Semua Organisasi)
     */
    private function seedPengurus(): void
    {
        $this->command->info('👥 Seeding 80 Pengurus (Campur Semua Organisasi)...');

        $banoms = array_keys(Pengurus::BANOMS);
        $positions = array_merge(
            Pengurus::JABATAN_INTI,
            Pengurus::JABATAN_BIDANG_UMUM,
            ['Anggota']
        );

        $names = [
            'Ahmad Wijaya', 'Budi Santoso', 'Citra Dewi', 'Dedi Gunawan', 'Eka Prasetya',
            'Fajar Hermawan', 'Gina Kusuma', 'Hendra Pratama', 'Irawan Hidayat', 'Joko Susilo',
            'Karina Sari', 'Lestari Budiarti', 'Muhammad Ridho', 'Nina Rahmawati', 'Oktavian Putra',
            'Prisca Handoko', 'Quirino Santos', 'Rini Marlina', 'Suryanto Wijaya', 'Tita Setiawan',
            'Usman Harahap', 'Vita Kusuma', 'Wawan Hermawan', 'Xenia Rahmawati', 'Yusuf Pratama',
            'Zuhria Sari', 'Adi Purnama', 'Bima Kusuma', 'Citra Handoko', 'Dimas Pratama',
            'Eka Santoso', 'Fadilla Dewi', 'Gani Wijaya', 'Hadi Kusuma', 'Ika Marlina',
            'Jendra Santoso', 'Karim Hermawan', 'Lita Rahmawati', 'Mitra Wijaya', 'Nita Kusuma',
        ];

        $counter = 0;
        $banomPercentage = 80 / count($banoms);

        foreach ($banoms as $banom) {
            for ($j = 0; $j < $banomPercentage; $j++) {
                if ($counter >= 80) {
                    break;
                }

                Pengurus::create([
                    'nama' => $names[$counter % count($names)].' '.($j + 1),
                    'jabatan' => $positions[$counter % count($positions)],
                    'banom' => $banom,
                    'label_banom' => Pengurus::BANOMS[$banom]['label'] ?? $banom,
                    'foto' => null,
                    'urutan' => $counter + 1,
                    'created_at' => now()->subDays(rand(0, 180)),
                    'updated_at' => now()->subDays(rand(0, 180)),
                ]);

                $counter++;

                if ($counter % 10 === 0) {
                    $this->command->line("  Generated $counter/80 pengurus...");
                }
            }
        }

        $this->command->info('  ✅ 80 pengurus created (distributed across all banoms)');
    }

    /**
     * Show final summary
     */
    private function showSummary(): void
    {
        $this->command->newLine();
        $this->command->line('╔════════════════════════════════════════════════════════════════╗');
        $this->command->line('║              📊 DATA SEEDING SUMMARY                            ║');
        $this->command->line('╚════════════════════════════════════════════════════════════════╝');
        $this->command->newLine();

        $this->command->line('✅ Berita:        '.Berita::count().' records');
        $this->command->line('✅ Agenda:        '.Agenda::count().' records');
        $this->command->line('✅ Gallery:       '.Gallery::count().' images');
        $this->command->line('✅ Infaq:         '.Infaq::count().' transactions');
        $this->command->line('✅ Aspirasi:      '.Aspirasi::count().' records');
        $this->command->line('✅ Pengurus:      '.Pengurus::count().' records');

        $this->command->newLine();
        $this->command->line('💾 Total Data Points: '.(
            Berita::count() +
            Agenda::count() +
            Gallery::count() +
            Infaq::count() +
            Aspirasi::count() +
            Pengurus::count()
        ));

        $this->command->newLine();
        $this->command->line('🎉 Data seeding completed successfully!');
    }
}
