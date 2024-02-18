<x-util.modal-button>
    <x-slot:img>
        <x-svg.rate-image class="w-10 h-10 cursor-pointer" />
    </x-slot:img>

    <x-slot:name>RateAll</x-slot:name>
    
    <livewire:lineups.rate-all-players
        :$lineups
        :$fixtureId />
</x-util.modal-button>