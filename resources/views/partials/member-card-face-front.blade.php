{{--
    Sisi depan kartu anggota (desain yang sudah ada dipertahankan).
    Butuh: $member, $qrCode
--}}
<div class="mc-side mc-side-front">
    <div class="mc-card-header">
        <h6>KARTU TANDA ANGGOTA</h6>
    </div>

    <div class="mc-card-content">
        @if ($member->photo)
            <img src="{{ $member->photo_url }}" class="mc-photo" alt="Foto {{ $member->full_name }}">
        @else
            <div class="mc-photo mc-photo-placeholder">No Photo</div>
        @endif

        <div class="mc-details">
            <p><strong>Nama</strong>: {{ $member->full_name }}</p>
            <p><strong>NIK</strong>: {{ $member->nik }}</p>
            <p><strong>No Reg</strong>: {{ $member->member_card_no ?? '—' }}</p>
            <p><strong>Banom</strong>: {{ $member->organisasi_label ?? '—' }}</p>
            <p><strong>Alamat</strong>: {{ Str::limit($member->address, 32) }}</p>
        </div>
    </div>

    <div class="mc-card-footer">
        <span class="mc-valid">Berlaku Anggota Aktif</span>
        <div class="mc-qr">
            {!! $qrCode !!}
        </div>
    </div>
</div>
