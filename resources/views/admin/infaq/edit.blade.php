<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ ($arah ?? $infaq->arah ?? 'masuk') === 'keluar' ? 'Edit Pengeluaran' : 'Edit Pemasukan (Internal)' }}
        </h2>
    </x-slot>

    <div class="py-6">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">

                <div class="mb-4 flex items-center gap-2 text-sm text-slate-600">
                    <span class="font-semibold">Kode Transaksi:</span>
                    <span class="px-2 py-1 bg-slate-100 rounded-lg font-mono">{{ $infaq->kode_transaksi }}</span>
                    <span class="text-xs text-slate-400">(tidak dapat diubah)</span>
                </div>

                <form action="{{ route('admin.infaq.update', $infaq) }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')
                    @if (($arah ?? $infaq->arah ?? 'masuk') === 'keluar')
                        @include('admin.infaq.p.form-keluar')
                    @else
                        @include('admin.infaq.p.form-masuk')
                    @endif

                    <div class="mt-6 flex gap-2">
                        <button type="submit"
                                class="bg-blue-600 hover:bg-blue-700 text-white px-5 py-2 rounded-lg">
                            Simpan Perubahan
                        </button>
                        <a href="{{ route('admin.infaq.index') }}"
                           class="bg-gray-200 hover:bg-gray-300 px-5 py-2 rounded-lg">
                            Batal
                        </a>
                    </div>
                </form>

            </div>
        </div>
    </div>
</x-app-layout>
