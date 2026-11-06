<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cetak KTA Masal</title>

    <!-- Google Fonts -->
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
            max-width: 1200px;
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

        .print-container {
            max-width: 1200px;
            margin: 0 auto;
        }

        /* ===== Print Styles ===== */
        @media print {
            body {
                background: white;
                padding: 0;
            }

            .no-print {
                display: none !important;
            }

            .page-break {
                page-break-after: always;
                break-after: page;
            }

            .page {
                width: 210mm;
                height: 297mm;
                page-break-after: always;
                background: white;
            }
        }

        /* CR80 Card (85.6mm x 53.9mm) in A4 */
        .card-grid {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 12mm;
            padding: 15mm;
            background: white;
            page-break-inside: avoid;
        }

        .kta-card {
            width: 85.6mm;
            height: 53.9mm;
            border: 1px solid #d1d5db;
            border-radius: 2mm;
            overflow: hidden;
            box-shadow: 0 1px 3px rgba(0,0,0,0.1);
            display: flex;
            flex-direction: column;
            background: white;
            position: relative;
            font-size: 11px;

            /* Prevent card break across pages */
            page-break-inside: avoid;
            break-inside: avoid;
        }

        .kta-header {
            background: linear-gradient(135deg, #059669 0%, #047857 100%);
            color: white;
            padding: 3mm;
            display: flex;
            align-items: center;
            gap: 2mm;
            font-size: 10px;
        }

        .kta-header img {
            width: 12mm;
            height: 12mm;
            object-fit: contain;
        }

        .kta-header-text {
            flex: 1;
        }

        .kta-header-text h6 {
            font-size: 9px;
            font-weight: 700;
            margin: 0;
            line-height: 1.2;
        }

        .kta-header-text small {
            font-size: 7px;
            font-weight: 400;
            display: block;
            margin-top: 1px;
        }

        .kta-body {
            flex: 1;
            display: flex;
            padding: 2.5mm;
            gap: 2mm;
            overflow: hidden;
        }

        .kta-photo {
            width: 17mm;
            height: 17mm;
            background: #f3f4f6;
            border: 0.5px solid #d1d5db;
            border-radius: 1mm;
            flex-shrink: 0;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 8px;
            color: #6b7280;
            font-weight: 600;
        }

        .kta-photo img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            border-radius: 0.5mm;
        }

        .kta-details {
            flex: 1;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            min-width: 0;
            font-size: 10px;
            line-height: 1.25;
        }

        .kta-detail-row {
            display: flex;
            gap: 2px;
            min-width: 0;
        }

        .kta-detail-label {
            font-weight: 700;
            color: #1f2937;
            white-space: nowrap;
            min-width: fit-content;
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
            padding: 2mm;
            border-top: 0.5px solid #e5e7eb;
            background: #f9fafb;
            font-size: 8px;
            height: 11mm;
        }

        .kta-status {
            font-weight: 700;
            color: #059669;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .kta-qr {
            width: 10mm;
            height: 10mm;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
        }

        .kta-qr img {
            width: 100%;
            height: 100%;
            object-fit: contain;
        }

        /* Screen view (non-print) */
        @media screen {
            .card-grid {
                grid-template-columns: repeat(auto-fill, minmax(350px, 1fr));
                gap: 16px;
                padding: 20px;
            }

            .kta-card {
                width: 100%;
                height: auto;
                transform: scale(1);
                box-shadow: 0 4px 6px rgba(0,0,0,0.1);
            }

            .kta-card:hover {
                box-shadow: 0 10px 15px rgba(0,0,0,0.15);
            }
        }

        .info-bar {
            background: #eff6ff;
            border: 1px solid #bfdbfe;
            border-radius: 8px;
            padding: 12px 16px;
            margin-bottom: 20px;
            font-size: 14px;
            color: #1e40af;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .info-bar svg {
            width: 20px;
            height: 20px;
            flex-shrink: 0;
        }
    </style>
</head>
<body>

    {{-- Toolbar (tidak tampil saat print) --}}
    <div class="no-print toolbar">
        <button class="btn" onclick="window.print()">🖨️ Cetak KTA</button>
        <button class="btn btn-secondary" onclick="window.history.back()">← Kembali</button>
    </div>

    <div class="no-print info-bar">
        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
        </svg>
        <span>{{ count($membersWithQr) }} Kartu akan dicetak. Format CR80 (85.6×53.9mm) — 2 kolom per lembar A4.</span>
    </div>

    {{-- Print Area --}}
    <div class="print-container">
        <div class="card-grid">
            @foreach ($membersWithQr as $item)
                @php
                    $member = $item['member'];
                    $qrCode = $item['qrCode'];
                @endphp
                <div class="kta-card">
                    {{-- Header dengan Logo --}}
                    <div class="kta-header">
                        <img src="{{ asset('images/logo.webp') }}" alt="Logo NU">
                        <div class="kta-header-text">
                            <h6>NU</h6>
                            <small>KARTU ANGGOTA</small>
                        </div>
                    </div>

                    {{-- Body: Foto + Detail --}}
                    <div class="kta-body">
                        {{-- Foto --}}
                        <div class="kta-photo">
                            @if ($member->photo)
                                <img src="{{ $member->photo_url }}" alt="Foto">
                            @else
                                <span>{{ strtoupper(substr($member->full_name, 0, 1)) }}</span>
                            @endif
                        </div>

                        {{-- Informasi --}}
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

                    {{-- Footer: Status + QR --}}
                    <div class="kta-footer">
                        <span class="kta-status">Aktif</span>
                        <div class="kta-qr">
                            {!! $qrCode !!}
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>

</body>
</html>
