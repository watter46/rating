<div id="result-{{ $name }}" class="w-full {{ $size }}"
    x-data="{
        rating: @entangle('player.ratings.my.rating'),
        mom: @entangle('player.ratings.my.mom')
    }"
    wire:ignore.self>
    
    <div class="flex justify-center">
        <div class="relative flex justify-center w-fit">
            <!-- PlayerImage -->
            <x-player.player-image
                class="{{ $size }} cursor-default"
                :number="$player['number']"
                :img="$player['img']" />

            <!-- Goals -->
            <div class="absolute top-0 left-0 -translate-x-[60%]">
                <x-player.goals
                    class="w-[13px] h-[13px] md:w-[14px] md:h-[14px]"
                    :goals="$player['goals']" />
            </div>

            <!-- Assists -->
            <div class="absolute top-0 right-0 translate-x-[60%]">
                <x-player.assists
                    class="w-[13px] h-[13px] md:w-[14px] md:h-[14px]"
                    :assists="$player['assists']" />
            </div>
            
            <!-- Rating -->
            <div class="absolute bottom-[-10%] left-[65%] min-w-[40px]">                
                <div class="flex items-center justify-center px-1 rounded-xl"
                    :style=" mom
                        ? 'background-color: #0E87E0'
                        : `background-color: ${ratingBgColor(rating)}`
                    ">

                    <template x-if="mom">
                        <p class="text-xs font-black text-gray-50">★</p>
                    </template>
                    
                    <p class="text-xs font-black md:text-sm text-gray-50"
                        x-text="ratingValue(rating)">
                    </p>
                </div>
            </div>
        </div>
    </div>

    <div class="flex items-center justify-center pointer-events-none gap-x-2">    
        <p class="text-xs font-black text-white md:text-sm">
            {{ $player['name'] }}
        </p>
    </div>

    @vite(['resources/js/rating.js'])
</div>
