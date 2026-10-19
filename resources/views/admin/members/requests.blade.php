
<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div class="flex items-center gap-3">
                <div class="p-2.5 bg-emerald-50 text-emerald-600 rounded-xl">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
                <div>
                    <h2 class="font-bold text-xl text-slate-800 leading-tight">
                        Permintaan Pendaftaran Anggota
                    </h2>
                    <p class="text-xs text-slate-500 mt-0.5">Tinjau permintaan pendaftaran, terima menjadi anggota aktif, atau tolak dengan alasan.</p>
                </div>
            </div>

            <a href="{{ route('members.index') }}"
               class="hidden sm:inline-flex items-center gap-2 bg-white hover:bg-slate-50 active:scale-95 text-slate-600 border border-slate-200 px-4 py-2.5 rounded-xl text-sm font-semibold transition-all shadow-xs">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/>
                </svg>
                <span>Data Anggota</span>
            </a>
        </div>
    </x-slot>

    <div class="py-8 bg-slate-50 min-h-[calc(100vh-4rem)]">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-4">

            {{-- Alert Sukses --}}
            @if (session('success'))
                <div class="p-4 bg-emerald-50 border border-emerald-200 text-emerald-800 rounded-2xl flex items-center gap-3 shadow-xs">
                    <svg class="w-5 h-5 text-emerald-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    <span class="text-sm font-medium">{{ session('success') }}</span>
                </div>
            @endif

            {{-- Alert Error --}}
            @if (session('error'))
                <div class="p-4 bg-rose-50 border border-rose-200 text-rose-800 rounded-2xl flex items-center gap-3 shadow-xs">
                    <svg class="w-5 h-5 text-rose-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    <span class="text-sm font-medium">{{ session('error') }}</span>
                </div>
            @endif

            {{-- Filter Status & Pencarian --}}
            <div class="bg-white rounded-2xl border border-slate-200/80 shadow-xs p-4 space-y-3">
                <div class="flex flex-wrap items-center justify-between gap-3">
                    <div class="flex flex-wrap gap-2">
                        @php
                            $tabs = [
                                'all' => 'Semua',
                                'pending' => 'Menunggu',
                                'accepted' => 'Diterima',
                                'rejected' => 'Ditolak',
                            ];
                            $tabQuery = array_filter(['q' => $search !== '' ? $search : null]);
                        @endphp

                        @foreach ($tabs as $value => $label)
                            <a href="{{ route('admin.members.requests', array_merge($tabQuery, $value === 'all' ? [] : ['status' => $value])) }}"
                               @class([
                                   'px-3.5 py-2 rounded-xl text-xs font-bold transition-all active:scale-95 inline-flex items-center gap-1.5',
                                   'bg-emerald-600 text-white shadow-xs' => $statusFilter === $value,
                                   'bg-white text-slate-600 border border-slate-200 hover:border-emerald-300 hover:text-emerald-700' => $statusFilter !== $value,
                               ])>
                                {{ $label }}
                            </a>
                        @endforeach
                    </div>

                    <form action="{{ route('admin.members.requests') }}" method="GET" class="flex items-center gap-2">
                        @if ($statusFilter !== 'all')
                            <input type="hidden" name="status" value="{{ $statusFilter }}">
                        @endif
                        <div class="relative">
                            <svg class="w-4 h-4 text-slate-400 absolute left-3 top-1/2 -translate-y-1/2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-4.35-4.35M17 10a7 7 0 11-14 0 7 7 0 0114 0z"/>
                            </svg>
                            <input type="search" name="q" value="{{ $search }}"
                                   placeholder="Cari nama, NIK, atau telepon..."
                                   class="w-full sm:w-64 pl-9 pr-3 py-2.5 rounded-xl border border-slate-200 bg-white text-sm text-slate-700 placeholder:text-slate-400 focus:outline-none focus:border-emerald-400 focus:ring-2 focus:ring-emerald-100 transition-all">
                        </div>
                        <button type="submit"
                                class="px-4 py-2.5 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white text-sm font-bold transition-all shadow-xs active:scale-95">
                            Cari
                        </button>
                        @if ($search !== '' || $statusFilter !== 'all')
                            <a href="{{ route('admin.members.requests') }}"
                               class="text-xs font-bold text-slate-400 hover:text-rose-600 transition-colors px-1">
                                Reset
                            </a>
                        @endif
                    </form>
                </div>
            </div>

            {{-- Tabel Permintaan --}}
            <div class="bg-white rounded-2xl border border-slate-200/80 shadow-xs overflow-hidden">
                <div class="p-5 border-b border-slate-100 flex items-center justify-between">
                    <h3 class="text-base font-bold text-slate-800">Daftar Permintaan Pendaftaran</h3>
                    <span class="text-xs font-semibold px-2.5 py-1 bg-slate-100 text-slate-600 rounded-lg">
                        Total: {{ $members->total() }} Data
                    </span>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full text-sm text-left border-collapse">
                        <thead>
                            <tr class="bg-slate-50 border-b border-slate-200/80 text-xs uppercase font-bold text-slate-500 tracking-wider">
                                <th class="px-6 py-4">Pendaftar</th>
                                <th class="px-6 py-4">NIK</th>
                                <th class="px-6 py-4">No. Kartu</th>
                                <th class="px-6 py-4">Tanggal Pengajuan</th>
                                <th class="px-6 py-4">Status</th>
                                <th class="px-6 py-4 text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            @forelse ($members as $member)
                                <tr data-member-id="{{ $member->id }}" class="hover:bg-slate-50/80 transition-colors">
                                    {{-- Pendaftar --}}
                                    <td class="px-6 py-4">
                                        <div class="flex items-center gap-3">
                                            @if ($member->photo)
                                                <img src="{{ asset('storage/' . $member->photo) }}"
                                                     alt="Foto {{ $member->full_name }}"
                                                     class="w-10 h-10 shrink-0 rounded-full object-cover border border-slate-200 bg-slate-50">
                                            @else
                                                <span class="w-10 h-10 shrink-0 rounded-full bg-emerald-50 text-emerald-700 inline-flex items-center justify-center font-bold text-sm">
                                                    {{ strtoupper(substr($member->full_name, 0, 1)) }}
                                                </span>
                                            @endif
                                            <div>
                                                <div class="font-bold text-slate-900">{{ $member->full_name }}</div>
                                                <div class="text-xs text-slate-400 mt-0.5">WA: {{ $member->phone }}</div>
                                                @if ($member->organisasi_label)
                                                    <span class="inline-flex items-center mt-1 px-2.5 py-0.5 rounded-full text-[11px] font-bold bg-emerald-100/80 text-emerald-800">
                                                        {{ $member->organisasi_label }}
                                                    </span>
                                                @endif
                                            </div>
                                        </div>
                                    </td>

                                    {{-- NIK --}}
                                    <td class="px-6 py-4 font-mono text-xs text-slate-600 whitespace-nowrap">
                                        {{ $member->nik }}
                                    </td>

                                    {{-- No Kartu --}}
                                    <td class="px-6 py-4">
                                        @if ($member->member_card_no)
                                            <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-md bg-slate-100 text-slate-700 font-mono text-xs font-semibold">
                                                {{ $member->member_card_no }}
                                            </span>
                                        @else
                                            <span class="text-slate-400 italic text-xs">Belum ada</span>
                                        @endif
                                    </td>

                                    {{-- Tanggal Pengajuan --}}
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm font-semibold text-slate-700">
                                            {{ $member->created_at?->translatedFormat('d M Y') ?? '—' }}
                                        </div>
                                        <div class="text-xs text-slate-400">
                                            {{ $member->created_at?->format('H:i') }} WIB
                                        </div>
                                    </td>

                                    {{-- Status Permintaan --}}
                                    <td class="px-6 py-4" data-cell="status">
                                        @include('admin.members.requests.status-cell')
                                    </td>

                                    {{-- Kolom Aksi --}}
                                    <td class="px-6 py-4 text-center" data-cell="actions">
                                        @include('admin.members.requests.actions-cell')
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="text-center py-12">
                                        <div class="flex flex-col items-center justify-center space-y-3">
                                            <div class="p-4 bg-slate-100 text-slate-400 rounded-full">
                                                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-4.35-4.35M17 10a7 7 0 11-14 0 7 7 0 0114 0z"/>
                                                </svg>
                                            </div>
                                            @if ($search !== '' || $statusFilter !== 'all')
                                                <p class="text-slate-500 font-medium">Tidak ada permintaan yang cocok dengan filter saat ini.</p>
                                                <a href="{{ route('admin.members.requests') }}" class="text-xs font-bold text-emerald-600 hover:underline">
                                                    Tampilkan semua permintaan
                                                </a>
                                            @else
                                                <p class="text-slate-500 font-medium">Belum ada permintaan pendaftaran anggota.</p>
                                                <a href="{{ route('members.register-form') }}" class="text-xs font-bold text-emerald-600 hover:underline">
                                                    Lihat halaman pendaftaran publik
                                                </a>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                @if ($members->hasPages())
                    <div class="p-4 border-t border-slate-100 bg-slate-50/50">
                        {{ $members->links() }}
                    </div>
                @endif
            </div>

        </div>
    </div>

    {{-- Dialog Konfirmasi: Terima --}}
    <x-modal name="confirm-accept-request" max-width="lg">
        <div class="p-6">
            <div class="flex items-start gap-4">
                <div class="shrink-0 p-3 bg-emerald-50 text-emerald-600 rounded-2xl">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
                <div class="pt-1">
                    <h3 class="text-lg font-bold text-slate-800">Terima Permintaan Pendaftaran?</h3>
                    <p class="text-sm text-slate-500 mt-2 leading-relaxed">
                        Data <span id="accept-member-name" class="font-bold text-slate-700"></span> akan disetujui
                        dan status keanggotaannya berubah menjadi
                        <strong class="text-emerald-700">anggota aktif</strong> lengkap dengan nomor kartu anggota.
                    </p>
                    <p id="accept-error" class="hidden mt-3 text-sm font-semibold text-rose-600 bg-rose-50 border border-rose-100 rounded-xl px-3 py-2"></p>
                </div>
            </div>

            <div class="mt-6 flex flex-col-reverse sm:flex-row sm:justify-end gap-3">
                <button type="button"
                        x-on:click="$dispatch('close-modal', 'confirm-accept-request')"
                        class="px-5 py-2.5 rounded-xl border border-slate-200 text-sm font-bold text-slate-600 hover:bg-slate-50 transition-all active:scale-95">
                    Batal
                </button>
                <button type="button" id="accept-confirm-btn"
                        class="px-5 py-2.5 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white text-sm font-bold transition-all shadow-xs active:scale-95 disabled:opacity-70 disabled:cursor-not-allowed">
                    Ya, Terima Pendaftaran
                </button>
            </div>
        </div>
    </x-modal>

    {{-- Dialog Konfirmasi: Tolak (dengan alasan) --}}
    <x-modal name="confirm-reject-request" max-width="lg">
        <div class="p-6">
            <div class="flex items-start gap-4">
                <div class="shrink-0 p-3 bg-rose-50 text-rose-600 rounded-2xl">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </div>
                <div class="pt-1 w-full">
                    <h3 class="text-lg font-bold text-slate-800">Tolak Permintaan Pendaftaran?</h3>
                    <p class="text-sm text-slate-500 mt-2 leading-relaxed">
                        Permintaan dari <span id="reject-member-name" class="font-bold text-slate-700"></span>
                        akan <strong class="text-rose-600">ditolak</strong>. Masukkan alasan penolakan
                        agar pendaftar memahami alasannya.
                    </p>

                    <label for="reject-reason" class="block text-sm font-bold text-slate-700 mt-4 mb-1.5">
                        Alasan Penolakan <span class="text-rose-500">*</span>
                    </label>
                    <textarea id="reject-reason" name="reason" rows="3" maxlength="500" required
                              placeholder="Contoh: Data NIK tidak sesuai dengan dokumen identitas."
                              class="w-full rounded-xl border border-slate-200 px-4 py-3 text-sm text-slate-700 placeholder:text-slate-400 focus:outline-none focus:border-rose-400 focus:ring-2 focus:ring-rose-100 transition-all"></textarea>

                    <div class="flex items-center justify-between mt-1.5 gap-3">
                        <p id="reject-error" class="hidden text-sm font-semibold text-rose-600"></p>
                        <span class="text-xs text-slate-400 ml-auto"><span id="reject-count">0</span>/500</span>
                    </div>
                </div>
            </div>

            <div class="mt-6 flex flex-col-reverse sm:flex-row sm:justify-end gap-3">
                <button type="button"
                        x-on:click="$dispatch('close-modal', 'confirm-reject-request')"
                        class="px-5 py-2.5 rounded-xl border border-slate-200 text-sm font-bold text-slate-600 hover:bg-slate-50 transition-all active:scale-95">
                    Batal
                </button>
                <button type="button" id="reject-confirm-btn"
                        class="px-5 py-2.5 rounded-xl bg-rose-600 hover:bg-rose-700 text-white text-sm font-bold transition-all shadow-xs active:scale-95 disabled:opacity-70 disabled:cursor-not-allowed">
                    Ya, Tolak Pendaftaran
                </button>
            </div>
        </div>
    </x-modal>

    {{-- Toast Notifikasi --}}
    <div id="request-toast"
         class="fixed bottom-5 right-5 z-[60] max-w-sm translate-y-3 opacity-0 pointer-events-none transition-all duration-300">
        <div class="flex items-center gap-3 bg-slate-900 text-white px-4 py-3 rounded-2xl shadow-xl text-sm font-semibold">
            <svg class="w-5 h-5 text-emerald-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
            <span id="request-toast-text"></span>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            var csrfToken = document.querySelector('meta[name="csrf-token"]')?.content ?? '';
            var targetUrl = '';
            var targetRow = null;
            var currentAction = null;
            var busy = false;

            var acceptNameEl = document.getElementById('accept-member-name');
            var acceptErrorEl = document.getElementById('accept-error');
            var acceptBtn = document.getElementById('accept-confirm-btn');
            var rejectNameEl = document.getElementById('reject-member-name');
            var rejectErrorEl = document.getElementById('reject-error');
            var rejectReasonEl = document.getElementById('reject-reason');
            var rejectCountEl = document.getElementById('reject-count');
            var rejectBtn = document.getElementById('reject-confirm-btn');
            var toastEl = document.getElementById('request-toast');
            var toastTextEl = document.getElementById('request-toast-text');
            var toastTimer = null;

            function show(el, message) {
                el.textContent = message;
                el.classList.remove('hidden');
            }

            function hide(el) {
                el.classList.add('hidden');
            }

            function showToast(message) {
                toastTextEl.textContent = message;
                toastEl.classList.add('translate-y-0', 'opacity-100');
                toastEl.classList.remove('translate-y-3', 'opacity-0');
                window.clearTimeout(toastTimer);
                toastTimer = window.setTimeout(function () {
                    toastEl.classList.remove('translate-y-0', 'opacity-100');
                    toastEl.classList.add('translate-y-3', 'opacity-0');
                }, 4000);
            }

            document.addEventListener('click', function (event) {
                var acceptTrigger = event.target.closest('[data-accept-btn]');
                var rejectTrigger = event.target.closest('[data-reject-btn]');

                if (acceptTrigger) {
                    targetUrl = acceptTrigger.dataset.url;
                    targetRow = acceptTrigger.closest('tr');
                    currentAction = 'accept';
                    acceptNameEl.textContent = acceptTrigger.dataset.name;
                    hide(acceptErrorEl);
                    acceptBtn.disabled = false;
                    acceptBtn.textContent = 'Ya, Terima Pendaftaran';
                    window.dispatchEvent(new CustomEvent('open-modal', { detail: 'confirm-accept-request' }));
                }

                if (rejectTrigger) {
                    targetUrl = rejectTrigger.dataset.url;
                    targetRow = rejectTrigger.closest('tr');
                    currentAction = 'reject';
                    rejectNameEl.textContent = rejectTrigger.dataset.name;
                    rejectReasonEl.value = '';
                    rejectCountEl.textContent = '0';
                    hide(rejectErrorEl);
                    rejectBtn.disabled = false;
                    rejectBtn.textContent = 'Ya, Tolak Pendaftaran';
                    window.dispatchEvent(new CustomEvent('open-modal', { detail: 'confirm-reject-request' }));
                    window.setTimeout(function () { rejectReasonEl.focus(); }, 200);
                }
            });

            rejectReasonEl.addEventListener('input', function () {
                rejectCountEl.textContent = String(rejectReasonEl.value.length);
            });

            acceptBtn.addEventListener('click', function () {
                submitAction({}, acceptBtn, 'confirm-accept-request');
            });

            rejectBtn.addEventListener('click', function () {
                var reason = rejectReasonEl.value.trim();
                if (!reason) {
                    show(rejectErrorEl, 'Alasan penolakan wajib diisi.');
                    rejectReasonEl.focus();
                    return;
                }
                submitAction({ reason: reason }, rejectBtn, 'confirm-reject-request');
            });

            function submitAction(body, button, modalName) {
                if (busy || !targetUrl) {
                    return;
                }

                busy = true;
                var originalLabel = button.textContent;
                button.disabled = true;
                button.textContent = 'Memproses...';
                hide(acceptErrorEl);
                hide(rejectErrorEl);

                fetch(targetUrl, {
                    method: 'PATCH',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': csrfToken,
                    },
                    body: JSON.stringify(body),
                })
                    .then(function (response) {
                        return response.json().catch(function () { return {}; }).then(function (data) {
                            return { ok: response.ok, data: data };
                        });
                    })
                    .then(function (result) {
                        if (!result.ok) {
                            var errors = result.data.errors || {};
                            var message = (errors.reason && errors.reason[0])
                                || result.data.message
                                || 'Terjadi kesalahan. Silakan coba lagi.';
                            show(currentAction === 'reject' ? rejectErrorEl : acceptErrorEl, message);
                            return;
                        }

                        applyUpdate(result.data);
                        window.dispatchEvent(new CustomEvent('close-modal', { detail: modalName }));
                        showToast(result.data.message || 'Data berhasil diperbarui.');
                    })
                    .catch(function () {
                        show(currentAction === 'reject' ? rejectErrorEl : acceptErrorEl,
                            'Gagal menghubungi server. Periksa koneksi Anda lalu coba lagi.');
                    })
                    .finally(function () {
                        busy = false;
                        button.disabled = false;
                        button.textContent = originalLabel;
                    });
            }

            function applyUpdate(data) {
                if (!targetRow || !data.html) {
                    return;
                }

                var statusCell = targetRow.querySelector('[data-cell="status"]');
                var actionsCell = targetRow.querySelector('[data-cell="actions"]');

                if (statusCell) {
                    statusCell.innerHTML = data.html.status;
                }
                if (actionsCell) {
                    actionsCell.innerHTML = data.html.actions;
                }

                targetRow.classList.add('bg-emerald-50/70');
                window.setTimeout(function () {
                    targetRow.classList.remove('bg-emerald-50/70');
                }, 1500);
            }
        });
    </script>
</x-app-layout>
