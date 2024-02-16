<div class="md:mr-5">
    <div class="flex flex-col justify-center w-full" title="RateAll" wire:click="$toggle('isOpen')">
        <div class="flex justify-center">
            <x-svg.rate-image class="w-10 h-10 cursor-pointer md:w-12 md:h-12 lg:w-8 lg:h-8" />
        </div>
        <p class="text-xs font-black text-center text-gray-400 md:text-lg lg:text-base">RateAll</p>
    </div>

    @if($isOpen)
        <div class="fixed top-0 left-0 z-10 flex items-center justify-center w-full h-screen p-2"
            style="background: rgba(31, 41, 55, 0.95);">
            <div class="flex flex-col w-full h-full overflow-auto bg-gray-800 border border-gray-700 rounded-lg">
                <header class="relative w-full p-3">
                    <p class="text-3xl font-black text-center text-gray-300 md:text-4xl">RateAll</p>
                    <div class="absolute top-0 right-0 p-2 border-gray-400 rounded-full cursor-pointer hover:border"
                        wire:click="$toggle('isOpen')">
                        <x-svg.cross-image class="w-10 h-10 md:w-12 md:h-12 fill-gray-400" />
                    </div>
                </header>
                
                {{-- AllPlayers --}}
                <livewire:lineups.rate-all-players
                    :$lineups
                    :$fixtureId />
            </div>
        </div>
    @endif
</div>