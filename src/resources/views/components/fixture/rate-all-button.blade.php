<x-util.modal-button dispatchName="rate-all">
    <x-slot:icon>
        <x-svg.rate-image class="w-8 h-8 cursor-pointer" />

        <p class="text-xs font-black text-center text-gray-400">
            RateAll
        </p>
    </x-slot:icon>

    <x-slot:disabled-icon></x-slot:disabled-icon>
    
    <livewire:rate-all.rate-all
        :$lineups
        :$fixtureId />
</x-util.modal-button>