<div class="w-screen h-screen flex items-center justify-center">
    <div class="max-w-lg mx-auto">
        <form wire:submit="create">
            {{ $this->form }}

            <button type="submit">
                Submit
            </button>
        </form>
    </div>

    <x-filament-actions::modals />
</div>
