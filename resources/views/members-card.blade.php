{{--
    Halaman "Kartu Anggota" — satu halaman untuk semua kondisi kartu:
    - Belum ada pengajuan      -> tombol Ajukan Pendaftaran
    - Menunggu persetujuan     -> info proses + tanggal pengajuan (tanpa tombol cetak)
    - Ditolak                  -> alasan penolakan + tombol Ajukan Kembali
    - Disetujui                -> data anggota + preview kartu 3D + cetak & download

    Partial yang dipakai kembali dari fitur lama:
    - partials.member-card-interactive (preview 3D, drag, flip)
    - partials.member-card-print (versi datar untuk cetak/download)
--}}
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kartu Anggota — NU Banjaranyar</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=Playfair+Display:wght@700&display=swap" rel="stylesheet">

    <style>
        :root {
            --bg-main: #f4f7f4;
            --surface: #ffffff;
            --surface-soft: #f8faf8;
            --ink-primary: #0f291e;
            --ink-secondary: #4a5d53;
            --ink-muted: #809388;
            --line: #e1e8e2;

            --brand-green: #0d6838;
            --brand-green-light: #128c4c;
            --brand-gold: #d97706;
            --brand-danger: #dc2626;

            --shadow-sm: 0 2px 8px rgba(15, 41, 30, 0.04);
            --shadow-md: 0 12px 32px rgba(15, 41, 30, 0.08);
            --shadow-lg: 0 24px 48px -12px rgba(15, 41, 30, 0.12);

            --radius-lg: 24px;
            --radius-md: 16px;
            --radius-sm: 12px;
            --container: 1080px;
        }

        * { box-sizing: border-box; }
        html { scroll-behavior: smooth; }
        body {
            margin: 0;
            font-family: "Plus Jakarta Sans", system-ui, -apple-system, sans-serif;
            color: var(--ink-primary);
            background: var(--bg-main);
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            -webkit-font-smoothing: antialiased;
        }

        img { max-width: 100%; display: block; }
        a { color: inherit; text-decoration: none; }

        .container { width: min(var(--container), calc(100% - 32px)); margin: 0 auto; }

        /* Header / Banner */
        .page-header {
            background: linear-gradient(135deg, #093820 0%, #0f522c 50%, #153c28 100%);
            color: #fff;
            padding: 64px 0 100px;
            position: relative;
            overflow: hidden;
        }
        .page-header::after {
            content: "";
            position: absolute;
            bottom: -1px;
            left: 0;
            right: 0;
            height: 48px;
            background: var(--bg-main);
            clip-path: ellipse(60% 100% at 50% 100%);
        }
        .header-bg-pattern {
            position: absolute;
            inset: 0;
            opacity: 0.08;
            background-image: radial-gradient(#fff 1px, transparent 1px);
            background-size: 24px 24px;
            pointer-events: none;
        }
        .page-header-inner { position: relative; z-index: 1; text-align: center; }

        .page-header .badge {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            background: rgba(255, 255, 255, 0.12);
            backdrop-filter: blur(8px);
            border: 1px solid rgba(255, 255, 255, 0.2);
            color: #d1fae5;
            font-size: 13px;
            font-weight: 700;
            padding: 8px 18px;
            border-radius: 999px;
            letter-spacing: 0.05em;
            text-transform: uppercase;
            margin-bottom: 18px;
        }
        .page-header h1 {
            margin: 0;
            font-family: "Playfair Display", Georgia, serif;
            font-size: clamp(32px, 5vw, 48px);
            font-weight: 700;
            line-height: 1.15;
            letter-spacing: -0.01em;
        }
        .page-header p {
            margin: 16px auto 0;
            color: rgba(255, 255, 255, 0.85);
            font-size: 16px;
            line-height: 1.6;
            max-width: 640px;
        }

        /* Konten */
        main { flex: 1; position: relative; z-index: 3; padding-bottom: 80px; }

        .search-card {
            background: var(--surface);
            border: 1px solid rgba(15, 41, 30, 0.06);
            border-radius: var(--radius-lg);
            box-shadow: var(--shadow-lg);
            padding: 40px;
            max-width: 760px;
            margin: -48px auto 0;
            position: relative;
            z-index: 5;
        }

        .search-card h2 {
            margin: 0 0 6px;
            font-size: 20px;
            font-weight: 800;
        }
        .search-card .hint {
            margin: 0 0 24px;
            font-size: 14px;
            color: var(--ink-secondary);
            line-height: 1.6;
        }

        .search-form { display: flex; gap: 12px; }
        .search-form .search-field { flex: 1; }
        .search-form input {
            flex: 1;
            width: 100%;
            padding: 15px 18px;
            border: 1.5px solid var(--line);
            border-radius: var(--radius-sm);
            font-family: inherit;
            font-size: 15px;
            color: var(--ink-primary);
            background: var(--surface-soft);
            outline: none;
            transition: all 200ms ease;
        }
        .search-form input:focus {
            border-color: var(--brand-green);
            box-shadow: 0 0 0 4px rgba(13, 104, 56, 0.12);
            background: #fff;
        }
        .search-form input::placeholder { color: var(--ink-muted); }

        .btn {
            padding: 15px 30px;
            border-radius: 999px;
            font-family: inherit;
            font-size: 15px;
            font-weight: 700;
            cursor: pointer;
            border: none;
            transition: all 200ms cubic-bezier(0.4, 0, 0.2, 1);
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
        }
        .btn:hover { transform: translateY(-2px); }
        .btn:active { transform: translateY(0); }
        .btn-primary {
            background: linear-gradient(135deg, var(--brand-green) 0%, var(--brand-green-light) 100%);
            color: #fff;
            box-shadow: 0 8px 20px rgba(13, 104, 56, 0.25);
        }
        .btn-outline {
            background: var(--surface-soft);
            color: var(--brand-green);
            border: 1.5px solid var(--line);
        }
        .btn-outline:hover { background: #e8eee9; }

        /* Kartu status (panel kondisi) */
        .state-card {
            display: flex;
            align-items: flex-start;
            gap: 16px;
            border-radius: var(--radius-md);
            border: 1px solid;
            padding: 20px 22px;
            margin: 0 0 24px;
        }
        .state-card .state-icon {
            width: 44px;
            height: 44px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
        }
        .state-card h3 { margin: 0 0 6px; font-size: 17px; font-weight: 800; }
        .state-card p { margin: 0; font-size: 14.5px; color: var(--ink-secondary); line-height: 1.6; }
        .state-card .state-meta {
            margin-top: 10px;
            font-size: 13px;
            color: var(--ink-muted);
            font-weight: 600;
        }

        .state-pending {
            background: #fffbeb;
            border-color: #fde68a;
        }
        .state-pending .state-icon { background: #fef3c7; color: var(--brand-gold); }

        .state-rejected {
            background: #fef2f2;
            border-color: #fecaca;
        }
        .state-rejected .state-icon { background: #fee2e2; color: var(--brand-danger); }

        .state-approved {
            background: #ecfdf5;
            border-color: #a7f3d0;
        }
        .state-approved .state-icon { background: #d1fae5; color: #047857; }

        .state-warning {
            background: #fffbeb;
            border-color: #fde68a;
        }
        .state-warning .state-icon { background: #fef3c7; color: var(--brand-gold); }

        .reject-note {
            background: #fff;
            border: 1px solid #fecaca;
            border-left: 4px solid #b91c1c;
            border-radius: 12px;
            padding: 12px 16px;
            margin: 12px 0 0;
            font-size: 14px;
            line-height: 1.6;
            color: #7f1d1d;
        }
        .reject-note strong {
            display: block;
            margin-bottom: 4px;
            font-size: 12px;
            text-transform: uppercase;
            letter-spacing: 0.05em;
        }

        /* Hasil status */
        .result-card {
            background: var(--surface);
            border: 1px solid rgba(15, 41, 30, 0.06);
            border-radius: var(--radius-lg);
            box-shadow: var(--shadow-md);
            padding: 40px;
            max-width: 760px;
            margin: 28px auto 0;
        }

        .result-head {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 16px;
            flex-wrap: wrap;
            padding-bottom: 20px;
            margin-bottom: 24px;
            border-bottom: 1px dashed var(--line);
        }
        .result-head .name { font-size: 22px; font-weight: 800; margin: 0; }
        .result-head .nik { font-size: 13px; color: var(--ink-muted); margin: 4px 0 0; font-weight: 600; }

        .status-pill {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 8px 18px;
            border-radius: 999px;
            font-size: 13px;
            font-weight: 800;
            letter-spacing: 0.02em;
        }
        .status-pill::before {
            content: "";
            width: 8px;
            height: 8px;
            border-radius: 50%;
            background: currentColor;
        }
        .status-active { background: #ecfdf5; color: #047857; border: 1px solid #a7f3d0; }
        .status-pending { background: #fffbeb; color: #b45309; border: 1px solid #fde68a; }
        .status-none { background: #fef2f2; color: #b91c1c; border: 1px solid #fecaca; }

        /* Timeline Progres */
        .timeline { list-style: none; margin: 0 0 28px; padding: 0; }
        .timeline li {
            position: relative;
            padding: 0 0 24px 44px;
        }
        .timeline li:last-child { padding-bottom: 0; }
        .timeline li::before {
            content: "";
            position: absolute;
            left: 13px;
            top: 28px;
            bottom: 0;
            width: 2px;
            background: var(--line);
        }
        .timeline li:last-child::before { display: none; }

        .step-dot {
            position: absolute;
            left: 0;
            top: 0;
            width: 28px;
            height: 28px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            background: var(--surface-soft);
            border: 2px solid var(--line);
            color: var(--ink-muted);
            font-size: 13px;
            font-weight: 800;
        }
        .timeline li.done .step-dot { background: var(--brand-green); border-color: var(--brand-green); color: #fff; }
        .timeline li.done::before { background: var(--brand-green); }
        .timeline li.active-step .step-dot {
            background: #fffbeb;
            border-color: var(--brand-gold);
            color: var(--brand-gold);
            animation: pulse 1.6s ease-in-out infinite;
        }

        @keyframes pulse {
            0%, 100% { box-shadow: 0 0 0 0 rgba(217, 119, 6, 0.35); }
            50% { box-shadow: 0 0 0 8px rgba(217, 119, 6, 0); }
        }

        .step-title { font-size: 15px; font-weight: 800; margin: 2px 0 3px; }
        .step-desc { font-size: 13.5px; color: var(--ink-secondary); margin: 0; line-height: 1.55; }

        /* Detail Data */
        .detail-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 18px 24px;
            background: var(--surface-soft);
            border: 1px solid var(--line);
            border-radius: var(--radius-md);
            padding: 24px;
        }
        .detail-item .label {
            font-size: 11.5px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.06em;
            color: var(--ink-muted);
            margin-bottom: 4px;
        }
        .detail-item .value {
            font-size: 15px;
            font-weight: 700;
            color: var(--ink-primary);
            word-break: break-word;
        }
        .detail-item .value.mono { font-family: ui-monospace, SFMono-Regular, Menlo, monospace; }

        .result-actions {
            display: flex;
            gap: 14px;
            margin-top: 26px;
            padding-top: 22px;
            border-top: 1px solid var(--line);
        }
        .result-actions .btn { flex: 1; }

        /* Preview kartu + aksi cetak/download */
        .card-preview-head {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
            margin-top: 30px;
            padding-top: 26px;
            border-top: 1px dashed var(--line);
            font-size: 12.5px;
            font-weight: 800;
            letter-spacing: 1px;
            text-transform: uppercase;
            color: var(--ink-muted);
        }

        .card-actions {
            display: flex;
            flex-wrap: wrap;
            justify-content: center;
            gap: 12px;
            margin-top: 24px;
        }

        .download-bar {
            display: flex;
            flex-wrap: wrap;
            align-items: center;
            justify-content: center;
            gap: 10px;
            margin-top: 14px;
            padding: 14px 16px;
            background: var(--surface-soft);
            border: 1px dashed var(--line);
            border-radius: var(--radius-md);
        }
        .download-bar .dl-label {
            font-size: 13px;
            font-weight: 800;
            color: var(--ink-secondary);
            margin-right: 4px;
        }
        .btn-soft {
            padding: 10px 18px;
            border-radius: 999px;
            font-family: inherit;
            font-size: 13.5px;
            font-weight: 700;
            cursor: pointer;
            background: var(--surface);
            color: var(--brand-green);
            border: 1.5px solid var(--line);
            transition: all 200ms cubic-bezier(0.4, 0, 0.2, 1);
            display: inline-flex;
            align-items: center;
            gap: 6px;
        }
        .btn-soft:hover {
            background: #e8eee9;
            border-color: var(--brand-green);
            transform: translateY(-2px);
        }
        .btn-soft:active { transform: translateY(0); }

        .note {
            margin-top: 18px;
            font-size: 13px;
            color: var(--ink-muted);
            line-height: 1.6;
            text-align: center;
        }

        .empty-state {
            text-align: center;
            padding: 12px 0 4px;
        }
        .empty-state .icon {
            width: 56px;
            height: 56px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 16px;
            background: #fffbeb;
            color: var(--brand-gold);
        }
        .empty-state h3 { margin: 0 0 8px; font-size: 18px; font-weight: 800; }
        .empty-state p { margin: 0; font-size: 14.5px; color: var(--ink-secondary); line-height: 1.6; }

        .hint-foot {
            max-width: 760px;
            margin: 20px auto 0;
            text-align: center;
            font-size: 13.5px;
            color: var(--ink-muted);
            line-height: 1.6;
        }

        /* Footer */
        footer {
            margin-top: auto;
            background: #09140e;
            color: #94a39a;
            padding: 24px 0;
            font-size: 13.5px;
            border-top: 1px solid rgba(255,255,255,0.05);
        }
        .footer-inner { display: flex; align-items: center; justify-content: space-between; gap: 16px; }

        @media (max-width: 860px) {
            .search-card, .result-card { padding: 28px 22px; }
            .detail-grid { grid-template-columns: 1fr; }
            .footer-inner { flex-direction: column; align-items: center; text-align: center; gap: 8px; }
        }
        @media (max-width: 640px) {
            .page-header { padding: 40px 0 80px; }
            .search-form { flex-direction: column; }
            .search-form .search-field { width: 100%; }
            .result-actions { flex-direction: column; }
            .result-actions .btn { width: 100%; }
            .state-card { flex-direction: column; gap: 12px; }
            .download-bar { flex-direction: column; align-items: stretch; text-align: center; }
            .download-bar .btn-soft { justify-content: center; }
        }

        @media (prefers-reduced-motion: reduce) {
            .btn, .btn-soft { transition: none; }
            .btn:hover, .btn-soft:hover { transform: none; }
        }
    </style>

    @vite(['resources/css/app.css', 'resources/js/app.js', 'resources/js/member-card.js'])
</head>
<body>
    <x-navbar></x-navbar>

    <header class="page-header">
        <div class="header-bg-pattern"></div>
        <div class="container page-header-inner">
            <span class="badge">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><rect x="2" y="5" width="20" height="14" rx="2"/><path d="M2 10h20"/></svg>
                KARTANU Digital
            </span>
            <h1>Kartu Anggota</h1>
            <p>Satu halaman untuk melihat status pengajuan keanggotaan Anda — dan langsung mencetak atau mengunduh kartu anggota begitu disetujui.</p>
        </div>
    </header>

    <main>
        <div class="container">
            <!-- Form Pencarian -->
            <div class="search-card">
                <h2>Cari Data Kartu Anggota</h2>
                <p class="hint">Masukkan 16 digit NIK yang digunakan saat mendaftar. Status diperbarui secara otomatis oleh sistem setiap kali ada perubahan oleh admin.</p>
                <form class="search-form" action="{{ route('members.card') }}" method="GET">
                    <div class="search-field">
                        <label for="nik" class="sr-only">NIK</label>
                        <input type="text" id="nik" name="nik" value="{{ request('nik') }}" placeholder="Masukkan 16 digit NIK"
                               maxlength="16" inputmode="numeric" autocomplete="off" required
                               oninput="this.value = this.value.replace(/[^0-9]/g, '');">
                    </div>
                    <div class="search-field">
                        <label for="phone" class="sr-only">Nomor HP yang terdaftar</label>
                        <input type="text" id="phone" name="phone" value="{{ request('phone') }}" placeholder="Nomor HP terdaftar (0812xxxx)"
                               inputmode="tel" autocomplete="tel"
                               oninput="this.value = this.value.replace(/[^0-9+\- ]/g, '');">
                    </div>
                    <button type="submit" class="btn btn-primary">Tampilkan Kartu</button>
                </form>
            </div>

            @if (request()->has('nik'))
                <div class="result-card">
                    @if ($notVerified)
                        <div class="empty-state">
                            <div class="icon">
                                <svg width="26" height="26" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M10.29 3.86 1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z"/><line x1="12" y1="9" x2="12" y2="13"/><line x1="12" y1="17" x2="12.01" y2="17"/></svg>
                            </div>
                            <h3>Data Tidak Ditemukan</h3>
                            <p>Maaf, data keanggotaan tidak dapat ditampilkan. Pastikan NIK dan nomor HP sesuai dengan yang terdaftar saat pengajuan.</p>
                        </div>

                        <div class="result-actions">
                            <a class="btn btn-outline" href="{{ route('members.card') }}">Kembali</a>
                        </div>
                    @elseif ($member)
                        @php
                            $state     = $member->registrationState();
                            $isActive  = $member->status === 'active';
                            $cardReady = $state === 'accepted' && (bool) $member->member_card_no;
                        @endphp

                        @if (session('success'))
                            <div class="state-card state-approved" style="margin-bottom: 18px;">
                                <span class="state-icon">
                                    <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>
                                </span>
                                <div>
                                    <h3>Berhasil</h3>
                                    <p>{{ session('success') }}</p>
                                </div>
                            </div>
                        @endif

                        <!-- Identitas + pill status -->
                        <div class="result-head">
                            <div>
                                <p class="name">{{ $member->full_name }}</p>
                                <p class="nik">NIK: {{ $member->nik }}</p>
                            </div>
                            @if ($state === 'rejected')
                                <span class="status-pill status-none">Pengajuan Ditolak</span>
                            @elseif ($state === 'accepted')
                                <span class="status-pill status-active">{{ $cardReady ? 'Disetujui / Aktif' : 'Disetujui' }}</span>
                            @else
                                <span class="status-pill status-pending">Menunggu Persetujuan</span>
                            @endif
                        </div>

                        {{-- ============ KONDISI 2: MENUNGGU PERSETUJUAN ============ --}}
                        @if ($state === 'pending')
                            <div class="state-card state-pending">
                                <span class="state-icon">
                                    <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
                                </span>
                                <div>
                                    <h3>Menunggu Persetujuan</h3>
                                    <p>Pengajuan keanggotaan Anda sedang dalam proses pemeriksaan oleh admin. Anda belum dapat mencetak kartu sampai pengajuan disetujui.</p>
                                    <p class="state-meta">
                                        Tanggal pengajuan:
                                        <strong>{{ $member->created_at?->translatedFormat('d F Y') ?? '-' }}</strong>
                                        pukul {{ $member->created_at?->format('H:i') }} WIB
                                    </p>
                                </div>
                            </div>

                            <!-- Timeline Progres Pendaftaran -->
                            <ul class="timeline">
                                <li class="done">
                                    <span class="step-dot">✓</span>
                                    <p class="step-title">1. Pendaftaran Diterima</p>
                                    <p class="step-desc">
                                        Data Anda telah masuk ke sistem pada
                                        {{ $member->created_at?->translatedFormat('d F Y') ?? '-' }} pukul
                                        {{ $member->created_at?->format('H:i') }} WIB.
                                    </p>
                                </li>

                                <li class="active-step">
                                    <span class="step-dot">2</span>
                                    <p class="step-title">2. Verifikasi Admin</p>
                                    <p class="step-desc">
                                        Pengajuan Anda sedang diperiksa oleh admin. Setelah disetujui,
                                        status keanggotaan berubah menjadi aktif dan nomor kartu diterbitkan.
                                    </p>
                                </li>

                                <li class="">
                                    <span class="step-dot">3</span>
                                    <p class="step-title">3. Kartu Tanda Anggota</p>
                                    <p class="step-desc">
                                        Nomor kartu akan diterbitkan setelah pengajuan disetujui admin.
                                    </p>
                                </li>
                            </ul>

                        {{-- ============ KONDISI 3: DITOLAK ============ --}}
                        @elseif ($state === 'rejected')
                            <div class="state-card state-rejected">
                                <span class="state-icon">
                                    <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><line x1="15" y1="9" x2="9" y2="15"/><line x1="9" y1="9" x2="15" y2="15"/></svg>
                                </span>
                                <div>
                                    <h3>Pengajuan Ditolak</h3>
                                    <p>Pengajuan keanggotaan Anda belum dapat disetujui.</p>
                                    @if ($member->rejection_reason)
                                        <div class="reject-note">
                                            <strong>Alasan Penolakan</strong>
                                            {{ $member->rejection_reason }}
                                        </div>
                                    @endif
                                    <p class="state-meta">
                                        Diajukan pada {{ $member->created_at?->translatedFormat('d F Y') ?? '-' }}.
                                    </p>
                                </div>
                            </div>

                        {{-- ============ KONDISI 4: DISETUJUI ============ --}}
                        @else
                            @if ($cardReady)
                                <div class="state-card state-approved">
                                    <span class="state-icon">
                                        <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>
                                    </span>
                                    <div>
                                        <h3>Pengajuan Disetujui — Kartu Siap Digunakan</h3>
                                        <p>Selamat! Pengajuan keanggotaan Anda disetujui admin. Kartu anggota Anda aktif dan siap dicetak atau diunduh langsung di bawah ini.</p>
                                        <p class="state-meta">
                                            Status keanggotaan: <strong>Aktif</strong>
                                            @if ($member->member_card_no) — No. Registrasi <strong>{{ $member->member_card_no }}</strong> @endif
                                        </p>
                                    </div>
                                </div>
                            @else
                                <div class="state-card state-approved">
                                    <span class="state-icon">
                                        <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>
                                    </span>
                                    <div>
                                        <h3>Pengajuan Disetujui</h3>
                                        <p>Pengajuan Anda sudah disetujui. Nomor kartu sedang diterbitkan oleh admin — kartu dapat dicetak setelah nomor registrasi tersedia.</p>
                                    </div>
                                </div>
                            @endif
                        @endif

                        <!-- Detail Data Anggota -->
                        <div class="detail-grid">
                            <div class="detail-item">
                                <div class="label">Nama Lengkap</div>
                                <div class="value">{{ $member->full_name }}</div>
                            </div>
                            <div class="detail-item">
                                <div class="label">No. WhatsApp</div>
                                <div class="value">{{ $member->phone }}</div>
                            </div>
                            <div class="detail-item">
                                <div class="label">Status Keanggotaan</div>
                                <div class="value">
                                    @if ($state === 'rejected')
                                        Ditolak
                                    @elseif ($cardReady)
                                        Aktif
                                    @elseif ($state === 'accepted')
                                        Disetujui
                                    @else
                                        Belum Aktif (Menunggu Verifikasi)
                                    @endif
                                </div>
                            </div>
                            <div class="detail-item">
                                <div class="label">Nomor Registrasi Kartu</div>
                                <div class="value mono">{{ $member->member_card_no ?? '—' }}</div>
                            </div>
                        </div>

                        {{-- ============ AKSI / KARTU ============ --}}
                        @if ($cardReady)
                            <!-- Preview kartu interaktif (efek 3D, drag, flip) -->
                            <div class="card-preview-head">
                                <span>Preview Tampilan Kartu</span>
                            </div>

                            @include('partials.member-card-interactive')

                            <!-- Cetak & Download -->
                            <div class="card-actions">
                                <button type="button" class="btn btn-primary" onclick="printMemberCard('both')">
                                    🖨️ Cetak Kartu
                                </button>
                            </div>

                            <div class="download-bar">
                                <span class="dl-label">⬇️ Download Kartu:</span>
                                <button type="button" class="btn-soft" onclick="printMemberCard('front')">Depan</button>
                                <button type="button" class="btn-soft" onclick="printMemberCard('back')">Belakang</button>
                                <button type="button" class="btn-soft" onclick="printMemberCard('both')">Depan + Belakang</button>
                            </div>

                            <p class="note">
                                File dibuka sebagai dialog cetak browser — pilih <strong>Simpan sebagai PDF</strong> untuk menyimpan,
                                atau langsung kirim ke printer.
                            </p>
                        @elseif ($state === 'rejected')
                            <div class="result-actions">
                                <a class="btn btn-primary" href="{{ route('members.register-form') }}">Ajukan Kembali</a>
                                <a class="btn btn-outline" href="{{ route('landing') }}">Kembali ke Beranda</a>
                            </div>
                        @else
                            <div class="result-actions">
                                <a class="btn btn-outline" href="{{ route('landing') }}">Kembali ke Beranda</a>
                            </div>
                        @endif

                        <p class="note">
                            Ada perubahan data yang belum sesuai? Hubungi pengurus melalui WhatsApp untuk penyesuaian.
                        </p>
                    @else
                        {{-- ============ KONDISI 1: BELUM MENGAJUKAN ============ --}}
                        <div class="empty-state">
                            <div class="icon">
                                <svg width="26" height="26" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><line x1="19" y1="8" x2="19" y2="14"/><line x1="22" y1="11" x2="16" y2="11"/></svg>
                            </div>
                            <h3>Belum Ada Pengajuan</h3>
                            <p>
                                Anda belum mengajukan pendaftaran anggota.<br>
                                NIK <strong>{{ request('nik') }}</strong> tidak terdaftar dalam sistem.
                            </p>
                        </div>

                        <div class="result-actions">
                            <a class="btn btn-primary" href="{{ route('members.register-form') }}">Ajukan Pendaftaran</a>
                            <a class="btn btn-outline" href="{{ route('landing') }}">Kembali ke Beranda</a>
                        </div>
                    @endif
                </div>
            @endif

            <p class="hint-foot">
                Sudah mendaftar tapi lupa NIK-nya? Hubungi admin melalui WhatsApp untuk pengecekan data.
            </p>
        </div>
    </main>

    <footer>
        <div class="container footer-inner">
            <span>Copyright © {{ now()->year }} NU Ranting Banjaranyar. All rights reserved.</span>
            <span>Kontak Admin: <strong>nubanjaranyar@gmail.com</strong></span>
        </div>
    </footer>

    {{-- Versi datar kartu untuk cetak/download — harus child langsung <body> --}}
    @if (! empty($cardReady) && $qrCode)
        @include('partials.member-card-print')
    @endif
</body>
</html>
