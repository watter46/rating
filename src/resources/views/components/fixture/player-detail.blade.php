<div class="flex flex-col items-stretch w-full p-3">
    <div class="flex flex-col items-center justify-center gap-3">
        <x-player.player-image
            class="w-20 h-20"
            :number="$player['number']"
            :img="$player['img']" />

        <div class="flex justify-center w-full gap-x-3">                    
            <p class="text-sm font-bold text-center text-gray-100 md:text-2xl whitespace-nowrap">
                {{ $player['number'] }}
            </p> 
            <p class="text-sm font-bold text-center text-gray-100 md:text-2xl whitespace-nowrap">
                {{ $player['name'] }}
            </p>
        </div>
    </div>

    <div class="grid w-full grid-cols-3 mt-3 mb-3 text-center gap-x-0.5 gap-y-1">
        <!-- Position -->
        <div class="p-0.5">
            <p class="text-xs font-black text-gray-500 md:text-base">Position</p>
            <p class="text-base font-black text-gray-300 md:text-lg">{{ $player['position'] }}</p>
        </div>

        <!-- ShirtNumber -->
        <div class="p-0.5">
            <p class="text-xs font-black text-gray-500 md:text-base">ShirtNumber</p>
            <p class="text-base font-black text-gray-300 md:text-lg">{{ $player['number'] }}</p>
        </div>

        <!-- machineRating -->
        <div class="p-0.5">
            <p class="text-xs font-black text-gray-500 md:text-base">MachineRating</p>
            <p class="text-base font-black text-gray-300 md:text-lg">{{ $player['defaultRating'] }}</p>
        </div>
        
        <!-- Goals -->
        <div class="flex flex-col items-center p-0.5">
            <p class="text-xs font-black text-gray-500 md:text-base">Goals</p>
            <div class="flex items-center justify-center w-full h-full">
                <x-player.goals
                    class="w-[13px] h-[13px]"
                    :goals="$player['goal']" />
            </div>
        </div>
        
        <!-- Assists -->
        <div class="flex flex-col items-center p-0.5">
            <p class="text-xs font-black text-gray-500 md:text-base">Assists</p>
            <div class="flex items-center justify-center w-full h-full">
                <x-player.assists
                    class="w-[13px] h-[13px]"
                    :assists="$player['assists']" />
            </div>
        </div>
    </div>

    <!-- Rating -->
    <div class="flex items-center justify-center w-full h-full border-t-2 border-gray-700">
        <livewire:rating.rating
            :$fixtureId
            :$player
            :defaultRating="$player['defaultRating']"
            :key="$player['id']" />
    </div>
    
    @vite(['resources/css/rating.css'])
</div>