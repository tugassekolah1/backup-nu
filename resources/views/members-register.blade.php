<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Formulir Pendaftaran Anggota — NU Banjaranyar</title>
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
            --line-active: #10b981;
            
            --brand-green: #0d6838;
            --brand-green-light: #128c4c;
            --brand-accent: #84cc16;
            --brand-gold: #d97706;
            
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

        /* Header / Banner Modern */
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
            max-width: 580px; 
        }

        /* Notifikasi Alert Modern */
        .alert-wrapper { max-width: 760px; margin: -48px auto 24px; position: relative; z-index: 10; padding: 0 16px; }
        .alert {
            padding: 16px 20px; 
            border-radius: var(--radius-md);
            font-size: 14px; 
            font-weight: 500; 
            display: flex; 
            align-items: flex-start; 
            gap: 12px;
            box-shadow: var(--shadow-md);
        }
        .alert-success { background: #ecfdf5; border: 1px solid #a7f3d0; color: #065f46; }
        .alert-error { background: #fef2f2; border: 1px solid #fecaca; color: #991b1b; }
        .alert ul { margin: 6px 0 0; padding-left: 20px; }
        .alert li { margin-bottom: 2px; }

        /* Area Form Pendaftaran */
        .form-section { padding: 0 0 80px; position: relative; z-index: 3; }
        .form-card {
            background: var(--surface); 
            border: 1px solid rgba(15, 41, 30, 0.06); 
            border-radius: var(--radius-lg);
            box-shadow: var(--shadow-lg); 
            padding: 48px; 
            max-width: 760px; 
            margin: 0 auto;
        }
        
        .form-header-note {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 32px;
            padding-bottom: 16px;
            border-bottom: 1px dashed var(--line);
        }
        .form-note { font-size: 13px; color: var(--ink-secondary); margin: 0; }
        .form-note .req { color: #dc2626; font-weight: 700; }

        .form-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 24px; }
        .form-group { display: flex; flex-direction: column; gap: 8px; position: relative; }
        .form-group.full { grid-column: 1 / -1; }
        
        .form-group label { 
            font-size: 13px; 
            font-weight: 700; 
            color: var(--ink-primary); 
            letter-spacing: 0.01em; 
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .form-group label .req { color: #dc2626; margin-left: 2px; }

        /* Input Controls */
        .input-wrapper { position: relative; width: 100%; }
        
        .form-group input, 
        .form-group select, 
        .form-group textarea {
            width: 100%;
            padding: 14px 16px; 
            border: 1.5px solid var(--line); 
            border-radius: var(--radius-sm);
            font-family: inherit; 
            font-size: 15px; 
            color: var(--ink-primary); 
            background: var(--surface-soft);
            outline: none; 
            transition: all 200ms ease; 
        }

        .form-group input:hover, 
        .form-group select:hover, 
        .form-group textarea:hover {
            border-color: #b0c4b3;
            background: #fff;
        }

        .form-group input:focus, 
        .form-group select:focus, 
        .form-group textarea:focus {
            border-color: var(--brand-green); 
            box-shadow: 0 0 0 4px rgba(13, 104, 56, 0.12); 
            background: #fff;
        }
        
        .form-group input::placeholder, 
        .form-group textarea::placeholder { color: var(--ink-muted); }
        
        /* Handling Error State */
        .form-group.has-error input,
        .form-group.has-error select,
        .form-group.has-error textarea { 
            border-color: #ef4444; 
            background: #fff5f5; 
        }
        .form-group.has-error input:focus {
            box-shadow: 0 0 0 4px rgba(239, 68, 68, 0.12);
        }
        .form-error { 
            font-size: 12px; 
            color: #dc2626; 
            font-weight: 600; 
            margin: 4px 0 0; 
            display: flex;
            align-items: center;
            gap: 4px;
        }

        /* NIK Digit Counter Badge */
        .nik-counter { 
            font-size: 11px; 
            font-weight: 700; 
            color: var(--ink-muted); 
            background: var(--line);
            padding: 2px 8px;
            border-radius: 99px;
        }

        /* Custom Gender Selector Cards UI */
        .gender-options { display: grid; grid-template-columns: 1fr 1fr; gap: 12px; }
        .gender-card {
            border: 1.5px solid var(--line);
            background: var(--surface-soft);
            padding: 12px 16px;
            border-radius: var(--radius-sm);
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            font-size: 14px;
            font-weight: 600;
            color: var(--ink-secondary);
            transition: all 180ms ease;
            user-select: none;
        }
        .gender-card:hover { border-color: #b0c4b3; background: #fff; }
        .gender-card.active {
            border-color: var(--brand-green);
            background: rgba(13, 104, 56, 0.05);
            color: var(--brand-green);
        }
        
        /* Dropdown Select Standard Sizing */
        .form-group select {
            appearance: none;
            background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='16' height='16' viewBox='0 0 24 24' fill='none' stroke='%234a5d53' stroke-width='2' stroke-linecap='round' stroke-linejoin='round'%3E%3Cpath d='m6 9 6 6 6-6'/%3E%3C/svg%3E");
            background-repeat: no-repeat; 
            background-position: right 16px center; 
            padding-right: 44px; 
            cursor: pointer;
        }
        
        .form-group textarea { min-height: 110px; resize: vertical; line-height: 1.5; }

        /* Upload Foto */
        .form-group input[type="file"] {
            padding: 12px 14px;
            background: var(--surface-soft);
            color: var(--ink-secondary);
            font-size: 13.5px;
        }
        .form-group input[type="file"]::file-selector-button {
            margin-right: 12px;
            padding: 9px 18px;
            border: 1.5px solid var(--line);
            border-radius: 999px;
            background: #fff;
            color: var(--ink-primary);
            font-family: inherit;
            font-size: 13px;
            font-weight: 700;
            cursor: pointer;
            transition: all 180ms ease;
        }
        .form-group input[type="file"]::file-selector-button:hover {
            border-color: var(--brand-green);
            color: var(--brand-green);
        }

        .photo-hint {
            font-size: 12.5px;
            color: var(--ink-muted);
            margin: 0;
            line-height: 1.5;
        }

        .photo-preview-wrap {
            display: none;
            align-items: center;
            gap: 14px;
            margin-top: 10px;
            padding: 12px;
            background: var(--surface-soft);
            border: 1px dashed var(--line);
            border-radius: var(--radius-sm);
        }
        .photo-preview-wrap.show { display: flex; }
        .photo-preview {
            width: 74px;
            height: 96px;
            object-fit: cover;
            border-radius: 10px;
            border: 2px solid #fff;
            box-shadow: var(--shadow-sm);
            flex-shrink: 0;
        }
        .photo-preview-label { font-size: 13px; color: var(--ink-secondary); }

        /* Tombol & Aksi */
        .form-actions { 
            display: flex; 
            gap: 16px; 
            margin-top: 36px; 
            padding-top: 24px; 
            border-top: 1px solid var(--line); 
        }
        .btn {
            padding: 16px 32px; 
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
            gap: 10px;
        }
        .btn:hover { transform: translateY(-2px); }
        .btn:active { transform: translateY(0); }
        
        .btn-primary { 
            background: linear-gradient(135deg, var(--brand-green) 0%, var(--brand-green-light) 100%); 
            color: #fff; 
            box-shadow: 0 8px 20px rgba(13, 104, 56, 0.25); 
            flex: 2; 
        }
        .btn-primary:hover { box-shadow: 0 12px 28px rgba(13, 104, 56, 0.35); }
        .btn-primary:disabled { opacity: 0.65; cursor: not-allowed; transform: none; box-shadow: none; }
        
        .btn-secondary { 
            background: var(--surface-soft); 
            color: var(--ink-secondary); 
            border: 1.5px solid var(--line); 
            flex: 1;
        }
        .btn-secondary:hover { background: #e8eee9; color: var(--ink-primary); }

        .form-privacy { 
            font-size: 12.5px; 
            color: var(--ink-muted); 
            text-align: center; 
            margin: 20px 0 0; 
            line-height: 1.5; 
        }

        /* Indicator Spinner Submit */
        .spinner {
            width: 18px; 
            height: 18px; 
            border: 2.5px solid rgba(255,255,255,0.3); 
            border-top-color: #fff;
            border-radius: 50%; 
            animation: spin 0.6s linear infinite; 
            display: none;
        }
        .btn-primary.loading .spinner { display: inline-block; }
        .btn-primary.loading .btn-text { display: none; }
        @keyframes spin { to { transform: rotate(360deg); } }

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

        /* Responsive Breakpoints */
        @media (max-width: 860px) {
            .form-card { padding: 32px 24px; }
            .form-grid { grid-template-columns: 1fr; gap: 20px; }
            .footer-inner { flex-direction: column; align-items: center; text-align: center; gap: 8px; }
        }
        @media (max-width: 640px) {
            .page-header { padding: 40px 0 80px; }
            .form-actions { flex-direction: column; }
            .btn { width: 100%; }
        }

        /* Popup konfirmasi — selalu menutupi form (di atasnya) */
        .confirm-overlay {
            position: fixed;
            inset: 0;
            z-index: 1000;
            display: none;
            align-items: center;
            justify-content: center;
            padding: 16px;
            background: rgba(9, 40, 26, 0.55);
            backdrop-filter: blur(3px);
        }
        .confirm-overlay.show { display: flex; }
        .confirm-box {
            width: min(520px, 100%);
            max-height: calc(100vh - 32px);
            overflow-y: auto;
            background: var(--surface);
            border-radius: var(--radius-lg);
            box-shadow: var(--shadow-lg);
            animation: confirm-pop 180ms ease-out;
        }
        @keyframes confirm-pop {
            from { opacity: 0; transform: translateY(14px); }
            to { opacity: 1; transform: none; }
        }
        .confirm-head {
            background: linear-gradient(135deg, #093820 0%, #0f522c 100%);
            color: #fff;
            padding: 24px;
        }
        .confirm-head h2 { margin: 0 0 6px; font-size: 19px; }
        .confirm-head p { margin: 0; font-size: 13px; line-height: 1.6; color: #cfe8d9; }
        .confirm-list { margin: 0; padding: 10px 24px; }
        .confirm-list > div {
            display: flex;
            justify-content: space-between;
            align-items: baseline;
            gap: 16px;
            padding: 10px 0;
            border-bottom: 1px solid var(--line);
        }
        .confirm-list > div:last-child { border-bottom: none; }
        .confirm-list dt {
            flex-shrink: 0;
            font-size: 11px;
            font-weight: 800;
            letter-spacing: 0.05em;
            text-transform: uppercase;
            color: var(--ink-muted);
        }
        .confirm-list dd {
            margin: 0;
            font-size: 14px;
            font-weight: 700;
            color: var(--ink-primary);
            text-align: right;
            word-break: break-word;
        }
        .confirm-note {
            margin: 0;
            padding: 0 24px 4px;
            font-size: 12px;
            color: var(--ink-muted);
            text-align: center;
        }
        .confirm-actions { display: flex; gap: 12px; padding: 16px 24px 24px; }
        @media (max-width: 640px) {
            .confirm-actions { flex-direction: column; }
        }
    </style>

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body>
    <x-navbar></x-navbar>

    <header class="page-header">
        <div class="header-bg-pattern"></div>
        <div class="container page-header-inner">
            <span class="badge">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/></svg>
                Keanggotaan Resmi
            </span>
            <h1>Formulir Pendaftaran Anggota</h1>
            <p>Silakan lengkapi data diri Anda di bawah ini untuk bergabung sebagai anggota resmi NU Ranting Banjaranyar.</p>
        </div>
    </header>

    <div class="alert-wrapper" id="alertWrapper">
        @if (session('success'))
            <div class="alert alert-success">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="flex-shrink:0;"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><path d="m9 11 3 3L22 4"/></svg>
                <span>{{ session('success') }}</span>
            </div>
        @endif

        @if ($errors->any())
            <div class="alert alert-error">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="flex-shrink:0;"><circle cx="12" cy="12" r="10"/><line x1="12" x2="12" y1="8" y2="12"/><line x1="12" x2="12.01" y1="16" y2="16"/></svg>
                <div>
                    <strong>Terdapat beberapa kesalahan input:</strong>
                    <ul>
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            </div>
        @endif
    </div>

    <section class="form-section">
        <div class="container">
            <form class="form-card" action="{{ route('members.register') }}" method="POST" id="registerForm" novalidate enctype="multipart/form-data">
                @csrf

                <div class="form-header-note">
                    <span class="form-note">Kolom bertanda <span class="req">*</span> wajib diisi dengan benar.</span>
                </div>

                <div class="form-grid">
                    <!-- NIK Input -->
                    <div class="form-group @error('nik') has-error @enderror">
                        <label for="nik">
                            <span>NIK <span class="req">*</span></span>
                            <span class="nik-counter" id="nikCounter">0/16</span>
                        </label>
                        <div class="input-wrapper">
                            <input type="text" id="nik" name="nik" placeholder="16 digit NIK sesuai KTP"
                                   maxlength="16" inputmode="numeric" autocomplete="off"
                                   value="{{ old('nik') }}" required>
                        </div>
                        @error('nik')
                            <p class="form-error">
                                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
                                {{ $message }}
                            </p>
                        @enderror
                    </div>

                    <!-- Nama Lengkap Input -->
                    <div class="form-group @error('full_name') has-error @enderror">
                        <label for="full_name">Nama Lengkap <span class="req">*</span></label>
                        <div class="input-wrapper">
                            <input type="text" id="full_name" name="full_name" placeholder="Nama lengkap sesuai identitas"
                                   autocomplete="name" value="{{ old('full_name') }}" required>
                        </div>
                        @error('full_name')
                            <p class="form-error">
                                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
                                {{ $message }}
                            </p>
                        @enderror
                    </div>

                    <!-- Jenis Kelamin Selection -->
                    <div class="form-group @error('gender') has-error @enderror">
                        <label for="gender">Jenis Kelamin <span class="req">*</span></label>
                        
                        <!-- Interaktif Visual Cards (Sync ke hidden Select) -->
                        <div class="gender-options">
                            <div class="gender-card {{ old('gender') === 'L' ? 'active' : '' }}" onclick="selectGender('L')">
                                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="10" cy="14" r="5"/><path d="M19 5l-5.4 5.4"/><path d="M19 5h-5"/><path d="M19 5v5"/></svg>
                                Laki-laki
                            </div>
                            <div class="gender-card {{ old('gender') === 'P' ? 'active' : '' }}" onclick="selectGender('P')">
                                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="9" r="5"/><path d="M12 14v7"/><path d="M9 18h6"/></svg>
                                Perempuan
                            </div>
                        </div>

                        <!-- Native Select (Disimpan tersembunyi agar form request backend tetap 100% cocok) -->
                        <select id="gender" name="gender" required style="display: none;">
                            <option value="" disabled {{ old('gender') ? '' : 'selected' }}>Pilih jenis kelamin</option>
                            <option value="L" {{ old('gender') === 'L' ? 'selected' : '' }}>Laki-laki</option>
                            <option value="P" {{ old('gender') === 'P' ? 'selected' : '' }}>Perempuan</option>
                        </select>

                        @error('gender')
                            <p class="form-error">
                                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
                                {{ $message }}
                            </p>
                        @enderror
                    </div>

                    <!-- Phone Input -->
                    <div class="form-group @error('phone') has-error @enderror">
                        <label for="phone">No. WhatsApp / HP <span class="req">*</span></label>
                        <div class="input-wrapper">
                            <input type="tel" id="phone" name="phone" placeholder="Contoh: 081234567890"
                                   inputmode="numeric" autocomplete="tel" value="{{ old('phone') }}" required>
                        </div>
                        @error('phone')
                            <p class="form-error">
                                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
                                {{ $message }}
                            </p>
                        @enderror
                    </div>

                    <!-- Pilihan Organisasi / Banom -->
                    <div class="form-group full @error('organisasi') has-error @enderror">
                        <label for="organisasi">Organisasi / Banom yang Diikuti</label>
                        <div class="input-wrapper">
                            <select id="organisasi" name="organisasi">
                                @include('partials.organisasi-options', ['selected' => old('organisasi')])
                            </select>
                        </div>
                        <p class="photo-hint">
                            🏳️ Pilih badan otonom NU yang Anda ikuti (mis. Fatayat, Ansor, IPNU). Boleh dikosongkan bila belum mengikuti banom tertentu.
                        </p>
                        @error('organisasi')
                            <p class="form-error">
                                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
                                {{ $message }}
                            </p>
                        @enderror
                    </div>

                    <!-- Address Input -->
                    <div class="form-group full @error('address') has-error @enderror">
                        <label for="address">Alamat Lengkap <span class="req">*</span></label>
                        <div class="input-wrapper">
                            <textarea id="address" name="address" autocomplete="street-address" required
                                      placeholder="Jalan, RT/RW, Desa/Kelurahan, Kecamatan...">{{ old('address') }}</textarea>
                        </div>
                        @error('address')
                            <p class="form-error">
                                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
                                {{ $message }}
                            </p>
                        @enderror
                    </div>

                    <!-- Upload Foto -->
                    <div class="form-group full @error('photo') has-error @enderror">
                        <label for="photo">
                            <span>Foto Diri (Opsional)</span>
                            <span class="nik-counter">JPG/PNG · Maks 2 MB</span>
                        </label>
                        <div class="input-wrapper">
                            <input type="file" id="photo" name="photo"
                                   accept="image/jpeg,image/png"
                                   onchange="previewPhoto(event)">
                        </div>
                        <p class="photo-hint">
                            📸 Foto ini akan dicetak pada kartu tanda anggota Anda. Boleh dikosongkan dan bisa ditambahkan kemudian oleh admin.
                        </p>
                        <div class="photo-preview-wrap" id="photoPreviewWrap">
                            <img class="photo-preview" id="photoPreview" alt="Preview foto">
                            <span class="photo-preview-label">Foto siap diunggah — klik “Pilih file” lagi untuk mengganti.</span>
                        </div>
                        @error('photo')
                            <p class="form-error">
                                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
                                {{ $message }}
                            </p>
                        @enderror
                    </div>

                    <!-- Akun Login Anggota -->
                    <div class="form-group full">
                        <div style="border-top: 1.5px dashed var(--line); padding-top: 20px;">
                            <p style="margin: 0; font-size: 15px; font-weight: 800; color: var(--ink-primary);">Akun Login Anggota</p>
                            <p class="photo-hint" style="margin-top: 4px;">
                                🔑 Buat kata sandi sekarang. Setelah pendaftaran disetujui admin, Anda bisa masuk di halaman <strong>Masuk Anggota</strong> dengan NIK + kata sandi ini untuk mengubah data diri — perubahan langsung tampil di kartu Anda.
                            </p>
                        </div>
                    </div>

                    <div class="form-group @error('password') has-error @enderror">
                        <label for="password">
                            <span>Kata Sandi <span class="req">*</span></span>
                            <span class="nik-counter">Min. 8 karakter</span>
                        </label>
                        <div class="input-wrapper">
                            <input type="password" id="password" name="password" placeholder="Buat kata sandi"
                                   minlength="8" required autocomplete="new-password">
                        </div>
                        @error('password')
                            <p class="form-error">
                                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
                                {{ $message }}
                            </p>
                        @enderror
                    </div>

                    <div class="form-group @error('password_confirmation') has-error @enderror">
                        <label for="password_confirmation">
                            <span>Ulangi Kata Sandi <span class="req">*</span></span>
                        </label>
                        <div class="input-wrapper">
                            <input type="password" id="password_confirmation" name="password_confirmation" placeholder="Ketik ulang kata sandi"
                                   required autocomplete="new-password">
                        </div>
                        @error('password_confirmation')
                            <p class="form-error">
                                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
                                {{ $message }}
                            </p>
                        @enderror
                    </div>
                </div>

                <div class="form-actions">
                    <button type="submit" class="btn btn-primary" id="submitBtn">
                        <span class="spinner"></span>
                        <span class="btn-text">Daftar Anggota Sekarang</span>
                    </button>
                    <button type="reset" class="btn btn-secondary" onclick="resetGenderCards()">Reset Form</button>
                </div>

                <p class="form-privacy">
                    🔒 Data Anda dilindungi. Dengan mendaftar, Anda menyetujui data ini digunakan khusus untuk keperluan administrasi keanggotaan NU Ranting Banjaranyar.
                </p>

                <p class="form-privacy">
                    Sudah pernah mendaftar?
                    <a href="{{ route('members.card') }}" style="color: var(--brand-green); font-weight: 700; text-decoration: underline;">Lihat status pengajuan & kartu Anda di sini</a>.
                </p>

                <p class="form-privacy">
                    Sudah disetujui admin dan ingin mengubah data diri Anda?
                    <a href="{{ route('member.login') }}" style="color: var(--brand-green); font-weight: 700; text-decoration: underline;">Masuk di sini</a>.
                </p>
            </form>
        </div>
    </section>

    <footer>
        <div class="container footer-inner">
            <span>Copyright © {{ now()->year }} NU Ranting Banjaranyar. All rights reserved.</span>
            <span>Kontak Admin: <strong>nubanjaranyar@gmail.com</strong></span>
        </div>
    </footer>

    {{-- Popup konfirmasi: muncul di atas form saat tombol "Daftar" diklik --}}
    <div class="confirm-overlay" id="confirmRegister" role="dialog" aria-modal="true" aria-labelledby="confirmRegisterTitle">
        <div class="confirm-box">
            <div class="confirm-head">
                <h2 id="confirmRegisterTitle">Periksa Data Anda Dulu</h2>
                <p>Ini data yang akan dikirim ke admin. Kalau sudah benar, tekan tombol <strong>Kirim Pendaftaran</strong>. Kalau masih ada yang salah, tekan <strong>Kembali Perbaiki</strong>.</p>
            </div>

            <dl class="confirm-list">
                <div><dt>NIK</dt><dd id="cfNik">—</dd></div>
                <div><dt>Nama Lengkap</dt><dd id="cfNama">—</dd></div>
                <div><dt>Jenis Kelamin</dt><dd id="cfGender">—</dd></div>
                <div><dt>No. HP</dt><dd id="cfPhone">—</dd></div>
                <div><dt>Organisasi</dt><dd id="cfOrganisasi">—</dd></div>
                <div><dt>Alamat</dt><dd id="cfAddress">—</dd></div>
                <div><dt>Foto</dt><dd id="cfPhoto">—</dd></div>
            </dl>

            <p class="confirm-note">Data Anda hanya dipakai untuk administrasi keanggotaan NU Ranting Banjaranyar.</p>

            <div class="confirm-actions">
                <button type="button" class="btn btn-secondary" id="confirmBack">Kembali Perbaiki</button>
                <button type="button" class="btn btn-primary" id="confirmSend">Kirim Pendaftaran</button>
            </div>
        </div>
    </div>

    <script>
        // Filter angka-only untuk NIK & No. HP, plus counter digit NIK
        const nikInput = document.getElementById('nik');
        const nikCounter = document.getElementById('nikCounter');
        const phoneInput = document.getElementById('phone');
        const genderSelect = document.getElementById('gender');

        function updateNikCounter() {
            nikCounter.textContent = nikInput.value.length + '/16';
        }

        nikInput.addEventListener('input', function () {
            this.value = this.value.replace(/[^0-9]/g, '');
            updateNikCounter();
        });
        updateNikCounter();

        phoneInput.addEventListener('input', function () {
            this.value = this.value.replace(/[^0-9]/g, '');
        });

        // Helper untuk UI Kartu Gender
        function selectGender(value) {
            genderSelect.value = value;
            const cards = document.querySelectorAll('.gender-card');
            cards.forEach(card => card.classList.remove('active'));
            if (value === 'L') cards[0].classList.add('active');
            if (value === 'P') cards[1].classList.add('active');
        }

        function resetGenderCards() {
            const cards = document.querySelectorAll('.gender-card');
            cards.forEach(card => card.classList.remove('active'));
            genderSelect.value = "";
        }

        // Preview foto yang akan diunggah
        function previewPhoto(event) {
            const file = event.target.files[0];
            const wrap = document.getElementById('photoPreviewWrap');
            const preview = document.getElementById('photoPreview');

            if (!file) {
                wrap.classList.remove('show');
                preview.removeAttribute('src');
                return;
            }

            preview.src = URL.createObjectURL(file);
            wrap.classList.add('show');
        }

        // Loading state, cek isian wajib, lalu popup konfirmasi sebelum form dikirim
        const registerForm = document.getElementById('registerForm');
        const submitBtn = document.getElementById('submitBtn');
        const confirmPopup = document.getElementById('confirmRegister');
        const alertWrapper = document.getElementById('alertWrapper');
        const requiredFields = [
            { id: 'nik', label: 'NIK 16 digit' },
            { id: 'full_name', label: 'Nama Lengkap' },
            { id: 'gender', label: 'Jenis Kelamin' },
            { id: 'phone', label: 'No. HP' },
            { id: 'address', label: 'Alamat Lengkap' },
            { id: 'password', label: 'Kata Sandi (min. 8 karakter)' },
            { id: 'password_confirmation', label: 'Ulangi Kata Sandi' },
        ];

        function closeConfirmPopup() {
            confirmPopup.classList.remove('show');
        }

        function showFormHint(message) {
            alertWrapper.innerHTML =
                '<div class="alert alert-error">' +
                '<svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="flex-shrink:0;"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>' +
                '<span>' + message + '</span>' +
                '</div>';
            window.scrollTo({ top: 0, behavior: 'smooth' });
        }

        function fillConfirmPopup() {
            const valueOf = function (id) {
                const el = document.getElementById(id);
                return el && el.value.trim() ? el.value.trim() : '—';
            };

            document.getElementById('cfNik').textContent = valueOf('nik');
            document.getElementById('cfNama').textContent = valueOf('full_name');
            document.getElementById('cfGender').textContent =
                genderSelect.value === 'L' ? 'Laki-laki' : (genderSelect.value === 'P' ? 'Perempuan' : '—');
            document.getElementById('cfPhone').textContent = valueOf('phone');

            const organisasiSelect = document.getElementById('organisasi');
            document.getElementById('cfOrganisasi').textContent =
                organisasiSelect && organisasiSelect.value
                    ? organisasiSelect.options[organisasiSelect.selectedIndex].text
                    : 'Belum dipilih';

            document.getElementById('cfAddress').textContent = valueOf('address');

            const photoInput = document.getElementById('photo');
            document.getElementById('cfPhoto').textContent =
                photoInput && photoInput.files.length
                    ? 'Siap diunggah (' + photoInput.files[0].name + ')'
                    : 'Tidak ada (boleh dikosongkan)';
        }

        registerForm.addEventListener('submit', function (event) {
            // Jangan langsung kirim: pengguna diajak memeriksa data lewat popup dulu.
            event.preventDefault();

            const missingLabels = [];
            let firstMissingField = null;

            requiredFields.forEach(function (item) {
                const field = document.getElementById(item.id);
                if (!field) return;

                const isEmpty = !field.value || !String(field.value).trim();
                const badNik = item.id === 'nik' && field.value.replace(/[^0-9]/g, '').length !== 16;

                if (isEmpty || badNik) {
                    missingLabels.push(item.label);
                    if (!firstMissingField) firstMissingField = field;
                }
            });

            if (missingLabels.length) {
                showFormHint('Mohon lengkapi dulu: ' + missingLabels.join(', ') + '. Isi kolom tersebut, lalu tekan tombol Daftar lagi.');
                if (firstMissingField) {
                    firstMissingField.focus();
                    firstMissingField.scrollIntoView({ block: 'center' });
                }
                return;
            }

            const passwordField = document.getElementById('password');
            const confirmPasswordField = document.getElementById('password_confirmation');
            if (passwordField.value !== confirmPasswordField.value) {
                showFormHint('Kata Sandi dan Ulangi Kata Sandi tidak sama. Periksa kembali isian Anda.');
                confirmPasswordField.focus();
                confirmPasswordField.scrollIntoView({ block: 'center' });
                return;
            }

            alertWrapper.innerHTML = '';
            fillConfirmPopup();
            confirmPopup.classList.add('show');
        });

        document.getElementById('confirmBack').addEventListener('click', function () {
            closeConfirmPopup();
        });

        document.getElementById('confirmSend').addEventListener('click', function () {
            closeConfirmPopup();
            submitBtn.classList.add('loading');
            submitBtn.disabled = true;
            // submit() asli tidak memicu event submit lagi, jadi tidak terjadi loop.
            registerForm.submit();
        });

        confirmPopup.addEventListener('click', function (event) {
            if (event.target === confirmPopup) closeConfirmPopup();
        });

        document.addEventListener('keydown', function (event) {
            if (event.key === 'Escape') closeConfirmPopup();
        });
    </script>
</body>
</html>
