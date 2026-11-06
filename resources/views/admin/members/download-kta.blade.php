<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Download KTA - {{ $member->full_name }}</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">

    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Plus Jakarta Sans', Arial, sans-serif;
            background: #f3f4f6;
            padding: 20px;
        }

        .no-print {
            display: block;
        }

        .toolbar {
            max-width: 800px;
            margin: 0 auto 20px;
            display: flex;
            gap: 10px;
            justify-content: center;
            flex-wrap: wrap;
        }

        .btn {
            padding: 10px 20px;
            background: #059669;
            color: white;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            font-size: 14px;
            font-weight: 600;
            transition: background 0.2s;
        }

        .btn:hover {
            background: #047857;
        }

        .btn-secondary {
            background: #6b7280;
        }

        .btn-secondary:hover {
            background: #4b5563;
        }

        .container {
            max-width: 800px;
            margin: 0 auto;
            background: white;
            padding: 40px;
            border-radius: 12px;
            box-shadow: 0 1px 3px rgba(0,0,0,0.1);
        }

        .info-box {
            background: #eff6ff;
            border: 1px solid #bfdbfe;
            border-radius: 8px;
            padding: 12px 16px;
            margin-bottom: 30px;
            font-size: 13px;
            color: #1e40af;
            line-height: 1.5;
        }

        .card-preview {
            border: 2px dashed #d1d5db;
            border-radius: 8px;
            padding: 40px 20px;
            text-align: center;
            background: #fafafa;
            margin: 30px 0;
        }

        .kta-card {
            width: 250px;
            height: 157px;
            margin: 0 auto;
            border: 1px solid #d1d5db;
            border-radius: 3px;
            overflow: hidden;
            box-shadow: 0 4px 8px rgba(0,0,0,0.15);
            display: flex;
            flex-direction: column;
            background: white;
            font-size: 10px;
            page-break-inside: avoid;
        }

        .kta-header {
            background: linear-gradient(135deg, #059669 0%, #047857 100%);
            color: white;
            padding: 8px;
            display: flex;
            align-items: center;
            gap: 6px;
            font-size: 9px;
        }

        .kta-header img {
            width: 28px;
            height: 28px;
            object-fit: contain;
        }

        .kta-header-text {
            flex: 1;
        }

        .kta-header-text h6 {
            font-size: 9px;
            font-weight: 700;
            margin: 0;
            line-height: 1;
        }

        .kta-header-text small {
            font-size: 7px;
            font-weight: 400;
            display: block;
        }

        .kta-body {
            flex: 1;
            display: flex;
            padding: 6px;
            gap: 4px;
            overflow: hidden;
        }

        .kta-photo {
            width: 38px;
            height: 38px;
            background: #f3f4f6;
            border: 1px solid #d1d5db;
            border-radius: 2px;
            flex-shrink: 0;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 10px;
            color: #6b7280;
            font-weight: 600;
            overflow: hidden;
        }

        .kta-photo img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .kta-details {
            flex: 1;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            min-width: 0;
            font-size: 9px;
        }

        .kta-detail-row {
            display: flex;
            gap: 2px;
            line-height: 1.2;
        }

        .kta-detail-label {
            font-weight: 700;
            color: #1f2937;
            white-space: nowrap;
        }

        .kta-detail-value {
            flex: 1;
            color: #374151;
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
            font-weight: 500;
        }

        .kta-footer {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 4px;
            border-top: 1px solid #e5e7eb;
            background: #f9fafb;
            font-size: 8px;
            height: 24px;
        }

        .kta-status {
            font-weight: 700;
            color: #059669;
        }

        .kta-qr {
            width: 22px;
            height: 22px;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
        }

        .kta-qr img,
        .kta-qr svg {
            width: 100%;
            height: 100%;
            object-fit: contain;
        }

        .info-text {
            font-size: 12px;
            color: #6b7280;
            text-align: center;
            margin-top: 30px;
            line-height: 1.5;
        }

        @media print {
            body {
                background: white;
                padding: 0;
            }

            .no-print {
                display: none !important;
            }

            .container {
                max-width: 100%;
                padding: 0;
                background: transparent;
                box-shadow: none;
                border-radius: 0;
            }

            .info-box,
            .info-text,
            .card-preview {
                display: none !important;
            }

            .kta-card {
                width: 85.6mm;
                height: 53.9mm;
                margin: 0;
                box-shadow: none;
                page-break-inside: avoid;
            }
        }
    </style>
</head>
<body>

    {{-- Toolbar --}}
    <div class="no-print toolbar">
        <button class="btn" onclick="window.print()">🖨️ Cetak / Simpan PDF</button>
        <button class="btn btn-secondary" onclick="window.history.back()">← Kembali</button>
    </div>

    <div class="container">
        {{-- Info Box --}}
        <div class="no-print info-box">
            📋 Pratinjau Kartu Tanda Anggota (KTA). Klik tombol "Cetak / Simpan PDF" untuk mencetak atau menyimpan sebagai PDF.
        </div>

        {{-- Card Preview --}}
        <div class="no-print card-preview">
            <h3 style="margin-bottom: 20px; color: #374151;">Pratinjau KTA</h3>

            <div class="kta-card">
                {{-- Header --}}
                <div class="kta-header">
                    <img src="{{ asset('images/logo.webp') }}" alt="Logo NU">
                    <div class="kta-header-text">
                        <h6>NU</h6>
                        <small>KARTU ANGGOTA</small>
                    </div>
                </div>

                {{-- Body --}}
                <div class="kta-body">
                    {{-- Foto --}}
                    <div class="kta-photo">
                        @if ($member->photo)
                            <img src="{{ $member->photo_url }}" alt="Foto">
                        @else
                            {{ strtoupper(substr($member->full_name, 0, 1)) }}
                        @endif
                    </div>

                    {{-- Detail --}}
                    <div class="kta-details">
                        <div class="kta-detail-row">
                            <span class="kta-detail-label">Nama:</span>
                            <span class="kta-detail-value">{{ $member->full_name }}</span>
                        </div>
                        <div class="kta-detail-row">
                            <span class="kta-detail-label">NIK:</span>
                            <span class="kta-detail-value">{{ $member->nik }}</span>
                        </div>
                        <div class="kta-detail-row">
                            <span class="kta-detail-label">Kartu:</span>
                            <span class="kta-detail-value">{{ $member->member_card_no ?? '—' }}</span>
                        </div>
                        <div class="kta-detail-row">
                            <span class="kta-detail-label">Banom:</span>
                            <span class="kta-detail-value">{{ $member->organisasi_label ?? '—' }}</span>
                        </div>
                    </div>
                </div>

                {{-- Footer --}}
                <div class="kta-footer">
                    <span class="kta-status">Aktif</span>
                    <div class="kta-qr">
                        {!! $qrCode !!}
                    </div>
                </div>
            </div>

            <div class="info-text">
                Desain asli: CR80 (85.6×53.9mm)<br>
                Format cetak: A4 potrait
            </div>
        </div>

        {{-- Hidden Print Card (actual size) --}}
        <div style="display: none; page-break-after: always;">
            <div class="kta-card">
                <div class="kta-header">
                    <img src="{{ asset('images/logo.webp') }}" alt="Logo NU">
                    <div class="kta-header-text">
                        <h6>NU</h6>
                        <small>KARTU ANGGOTA</small>
                    </div>
                </div>

                <div class="kta-body">
                    <div class="kta-photo">
                        @if ($member->photo)
                            <img src="{{ $member->photo_url }}" alt="Foto">
                        @else
                            {{ strtoupper(substr($member->full_name, 0, 1)) }}
                        @endif
                    </div>

                    <div class="kta-details">
                        <div class="kta-detail-row">
                            <span class="kta-detail-label">Nama:</span>
                            <span class="kta-detail-value">{{ $member->full_name }}</span>
                        </div>
                        <div class="kta-detail-row">
                            <span class="kta-detail-label">NIK:</span>
                            <span class="kta-detail-value">{{ $member->nik }}</span>
                        </div>
                        <div class="kta-detail-row">
                            <span class="kta-detail-label">Kartu:</span>
                            <span class="kta-detail-value">{{ $member->member_card_no ?? '—' }}</span>
                        </div>
                        <div class="kta-detail-row">
                            <span class="kta-detail-label">Banom:</span>
                            <span class="kta-detail-value">{{ $member->organisasi_label ?? '—' }}</span>
                        </div>
                    </div>
                </div>

                <div class="kta-footer">
                    <span class="kta-status">Aktif</span>
                    <div class="kta-qr">
                        {!! $qrCode !!}
                    </div>
                </div>
            </div>
        </div>
    </div>

</body>
</html>
