<div id="result-{{ $name }}" class="w-full text-center"
    x-data="{
        rating: @entangle('rating'),
        mom: @entangle('mom')
    }"
    wire:ignore.self>
    
    <div class="flex justify-center">
        <div class="relative flex justify-center place-items-center">
            {{-- PlayerImage --}}
            <x-player.player-image
                class="{{ $size }}"
                :number="$player['number']"
                :img="$player['img']" />

            {{-- Goals --}}
            <div class="absolute top-0 left-0 -translate-x-[60%]">
                <x-player.goals class="w-[13px] h-[13px] md:w-6 md:h-6" :goals="$player['goal']" />
            </div>

            {{-- Assists --}}
            <div class="absolute top-0 right-0 translate-x-[60%]">
                <x-player.assists class="w-[13px] h-[13px] md:w-6 md:h-6" :assists="$player['assists']" />
            </div>
            
            {{-- Rating --}}
            <div class="absolute bottom-0 right-0 translate-x-[60%]">
                <div class="absolute bottom-0 right-0 translate-x-[60%]">
                    <div class="flex items-center justify-center w-8 md:w-12 md:py-1 rounded-xl"
                        :style=" mom
                            ? 'background-color: #0E87E0'
                            : `background-color: ${ratingBgColor(rating)}`
                        ">

                        @if($mom)
                            <p class="text-xs font-black text-gray-50 md:text-lg">★</p>
                        @endif
                        <p class="text-sm font-black text-gray-50 md:text-xl" x-text="ratingValue(rating)"></p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="flex justify-center gap-x-1 md:gap-x-2">
        <p class="font-black text-white md:text-2xl">{{ $player['number'] }}</p>
    
        <p class="font-black text-white md:text-2xl">{{ $this->toLastName() }}</p>
    </div>
    
    @vite(['resources/css/player.css', 'resources/js/rating.js'])
</div>