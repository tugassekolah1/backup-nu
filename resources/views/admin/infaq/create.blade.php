<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ ($arah ?? 'masuk') === 'keluar' ? 'Catat Pengeluaran' : 'Catat Pemasukan (Internal)' }}
        </h2>
    </x-slot>

    <div class="py-6">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">

                <form action="{{ route('admin.infaq.store') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    @if (($arah ?? 'masuk') === 'keluar')
                        @include('admin.infaq.p.form-keluar')
                    @else
                        @include('admin.infaq.p.form-masuk')
                    @endif

                    <div class="mt-6 flex gap-2">
                        <button type="submit"
                                class="{{ ($arah ?? 'masuk') === 'keluar' ? 'bg-rose-600 hover:bg-rose-700' : 'bg-emerald-600 hover:bg-emerald-700' }} text-white px-5 py-2 rounded-lg">
                            {{ ($arah ?? 'masuk') === 'keluar' ? 'Simpan Pengeluaran' : 'Simpan Pemasukan' }}
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
