<div class="px-6">
    <form wire:submit="create">
        <div class="max-w-3xl mx-auto my-10 px-8 py-10 bg-gray-100 rounded-lg shadow-lg">
            <h1 class="text-2xl text-center font-semibold">Pendaftaran MTQ Tingkat Kabupaten Trenggalek</h1>
            <p class="text-center mt-2">Tahun 2024</p>
        </div>
        {{ $this->form }}
        <div class="max-w-3xl mx-auto px-8 my-10">
            <button type="submit"
                class="max-w-3xl mx-auto px-6 py-3 bg-blue-600 text-white rounded-md shadow-md hover:bg-blue-700 focus:outline-none">
                Submit
            </button>
        </div>
    </form>

    <x-filament-actions::modals />

</div>
