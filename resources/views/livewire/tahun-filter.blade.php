<div class="flex items-center gap-2">
    <label class="text-sm font-medium text-gray-700 dark:text-gray-300">Tahun:</label>
    <select wire:model.live="selectedTahun" class="rounded-lg border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 dark:border-gray-600 dark:bg-gray-700 dark:text-white">
        @foreach($tahunList as $tahun)
            <option value="{{ $tahun->id }}">{{ $tahun->tahun }}</option>
        @endforeach
    </select>
</div>
