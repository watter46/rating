<x-util.modal-button class="w-full p-2 md:w-11/12 lg:w-10/12">
    <x-slot:icon>
        <x-svg.photo-image class="w-8 h-8 cursor-pointer" />

        <p class="text-xs font-black text-center text-gray-400">
            Result
        </p>
    </x-slot:icon>

    <x-slot:disabled-icon></x-slot:disabled-icon>
    
    <!-- Component -->
    <x-result.result
        :$fixtureData
        :$teamsData
        :$leagueData
        :$scoreData
        :$lineupsData
        :$fixtureInfoId />
</x-util.modal-button>