@php
    $selectedTahun = session('selected_tahun_id', \App\Models\Tahun::where('is_active', true)->first()?->id);
    $tahunList = \App\Models\Tahun::orderBy('tahun', 'desc')->get();
@endphp

<div class="flex items-center gap-2 px-3">
    <label class="text-sm font-medium text-gray-700 dark:text-gray-300">Tahun:</label>
    <form method="POST" action="/tahun-filter" class="flex items-center">
        @csrf
        <select name="tahun_id" onchange="this.form.submit()" class="rounded-lg border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 dark:border-gray-600 dark:bg-gray-700 dark:text-white text-sm">
            @foreach($tahunList as $tahun)
                <option value="{{ $tahun->id }}" {{ $selectedTahun == $tahun->id ? 'selected' : '' }}>
                    {{ $tahun->tahun }}
                </option>
            @endforeach
        </select>
    </form>
</div>
