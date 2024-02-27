<x-util.modal-button>
    <x-slot:img>
        <livewire:lineups.player
            name="substitutes"
            size="w-[40px] h-[40px] md:w-12 md:h-12"
            :$fixtureId
            :$player
            :key="$player['id']" />
    </x-slot:img>

    <x-slot:name></x-slot:name>
    
    <x-fixture.player-detail
        :$player 
        :$fixtureId />
</x-util.modal-button>