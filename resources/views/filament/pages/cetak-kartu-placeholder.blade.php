<x-filament-panels::page>
    @php
        $roleKey = match($kategori) {
            'Dewan Hakim' => 'dewan-hakim',
            'Panitera' => 'panitera',
            'Panitia' => 'panitia',
            default => 'dewan-hakim'
        };
        $bgFile = match($kategori) {
            'Dewan Hakim' => 'card_bg_dewan_hakim.png',
            'Panitera' => 'card_bg_panitera.png',
            'Panitia' => 'card_bg_panitia.png',
            default => 'card_bg_dewan_hakim.png'
        };
        $downloadUrl = route('admin.cetak-kartu.' . $roleKey . '.download');
    @endphp

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Preview Desain Kartu -->
        <div class="p-6 bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 flex flex-col items-center">
            <h4 class="text-sm font-bold text-gray-700 dark:text-gray-300 uppercase tracking-wider mb-4">
                Desain ID Card {{ $kategori }}
            </h4>
            <div class="w-56 rounded-lg overflow-hidden shadow-lg border border-gray-300 dark:border-gray-600">
                <img src="{{ asset('images/' . $bgFile) }}" alt="{{ $kategori }}" class="w-full h-auto">
            </div>
            <p class="text-xs text-gray-500 mt-3 text-center">
                Format resmi MTQ XXXIII Trenggalek 2026 (A4 PDF)
            </p>
        </div>

        <!-- Form Cetak & Download -->
        <div class="lg:col-span-2 p-6 bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700">
            <h3 class="text-lg font-bold text-gray-900 dark:text-white flex items-center gap-2">
                <x-heroicon-o-printer class="w-6 h-6 text-amber-600" />
                Cetak & Unduh ID Card {{ $kategori }}
            </h3>
            <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
                Masukkan nama-nama yang akan dicetak pada ID Card. Sistem akan langsung meng-generate file PDF siap print (4 kartu per lembar A4).
            </p>

            <form action="{{ $downloadUrl }}" method="POST" class="mt-6 space-y-4">
                @csrf
                <div>
                    <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-1">
                        Daftar Nama {{ $kategori }} (1 nama per baris untuk cetak massal):
                    </label>
                    <textarea 
                        name="names" 
                        rows="6" 
                        class="w-full rounded-lg border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-white shadow-sm focus:border-amber-500 focus:ring-amber-500 text-sm p-3"
                        placeholder="Contoh:&#10;H. AHMAD SYAFI'I, M.Ag&#10;DR. H. MUHAMMAD RIDWAN&#10;K.H. ABDULLAH MAHRUS"
                    ></textarea>
                    <p class="mt-1 text-xs text-gray-500">
                        *Kosongkan jika ingin mencetak template kartu tanpa nama / untuk ditulisi manual.
                    </p>
                </div>

                <div class="pt-2 flex items-center gap-3">
                    <button 
                        type="submit" 
                        class="inline-flex items-center gap-2 px-5 py-2.5 bg-amber-600 hover:bg-amber-700 text-white text-sm font-bold rounded-lg shadow-sm transition"
                    >
                        <x-heroicon-o-arrow-down-tray class="w-5 h-5" />
                        Download PDF ID Card {{ $kategori }}
                    </button>

                    <a 
                        href="{{ url('/admin/cetak-kartu/peserta') }}" 
                        class="inline-flex items-center gap-2 px-4 py-2.5 bg-gray-100 hover:bg-gray-200 dark:bg-gray-700 dark:hover:bg-gray-600 text-gray-700 dark:text-gray-200 text-sm font-semibold rounded-lg transition"
                    >
                        Ke Cetak Peserta
                    </a>
                </div>
            </form>
        </div>
    </div>
</x-filament-panels::page>
