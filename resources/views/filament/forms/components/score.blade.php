{{-- <input readonly class="text-9xl !important" {{ $applyStateBindingModifiers('wire:model') }}="{{ $getStatePath() }}" /> --}}
{{-- <div class="text-9xl">{{ $applyStateBindingModifiers('wire:model') }}="{{ $getStatePath() }}"</div> --}}

<div x-data="{ state: $wire.{{ $applyStateBindingModifiers("\$entangle('{$getStatePath()}')") }} }">
    <input
    className="text-xl text-stone-500" x-model="state" />
</div>
{{-- <div>
    {{ $getRecord()->total }}
</div> --}}
