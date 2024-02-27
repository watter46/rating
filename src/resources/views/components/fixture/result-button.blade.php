<x-util.modal-button class="w-full md:w-11/12 lg:w-2/3">
    <x-slot:icon>
        <x-svg.photo-image class="w-8 h-8 cursor-pointer" />

        <p class="text-xs font-black text-center text-gray-400">
            Result
        </p>
    </x-slot:icon>

    <x-slot:disabled-icon></x-slot:disabled-icon>
    
    <!-- Component -->
    <livewire:lineups.rated-result
        :$fixture
        :$teams
        :$league
        :$score
        :$lineups
        :$fixtureId />
</x-util.modal-button>