<x-util.modal-button>
    <x-slot:img>
        <x-svg.photo-image class="w-8 h-8 cursor-pointer" />
    </x-slot:img>

    <x-slot:name>Result</x-slot:name>
    
    <livewire:lineups.rated-result
        :$fixture
        :$teams
        :$league
        :$score
        :$lineups
        :$fixtureId />
</x-util.modal-button>