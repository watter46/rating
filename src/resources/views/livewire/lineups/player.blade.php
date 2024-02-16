<div id="{{ $name }}" class="hidden h-full {{ $size }}"
    x-data="{
        rating: @entangle('rating'),
        mom: @entangle('mom'),
        machine: @entangle('defaultRating')
    }"
    wire:ignore.self>
    
    <div class="flex justify-center player" wire:click="toDetail">
        <div class="relative flex justify-center w-fit place-items-center">
            {{-- PlayerImage --}}
            <x-player.player-image
                class="{{ $size }} cursor-pointer"
                :number="$player['number']"
                :img="$player['img']" />

            {{-- Goals --}}
            <div class="absolute top-0 left-0 -translate-x-[60%]">
                <x-player.goals
                    class="w-[13px] h-[13px] md:w-6 md:h-6 lg:w-[14px] lg:h-[14px]"
                    :goals="$player['goal']" />
            </div>

            {{-- Assists --}}
            <div class="absolute top-0 right-0 translate-x-[60%]">
                <x-player.assists
                    class="w-[13px] h-[13px] md:w-6 md:h-6 lg:w-[14px] lg:h-[14px]"
                    :assists="$player['assists']" />
            </div>
            
            {{-- Rating --}}
            @if ($isUser)
                <div class="absolute bottom-0 right-0 translate-x-[60%]">
                    <div class="flex items-center justify-center w-8 md:w-12 md:py-1 lg:p-0 rounded-xl"
                        :style=" mom
                            ? 'background-color: #0E87E0'
                            : `background-color: ${ratingBgColor(rating)}`
                        ">

                        @if($mom)
                            <p class="text-xs font-black text-gray-50 md:text-lg lg:text-base">★</p>
                        @endif
                        <p class="text-sm font-black text-gray-50 md:text-xl lg:text-base"
                            x-text="ratingValue(rating)">
                        </p>
                    </div>
                </div>
            @endif

            @unless($isUser)
                <div class="absolute bottom-0 right-0 translate-x-[60%]">
                    <div class="flex items-center justify-center w-8 md:w-12 lg:p-0 md:py-1 rounded-xl"
                        :style=" mom
                            ? 'background-color: #0E87E0'
                            : `background-color: ${ratingBgColor(machine)}`
                        ">

                        @if($mom)
                            <p class="text-xs font-black text-gray-50 md:text-lg lg:text-base">★</p>
                        @endif
                        <p class="text-sm font-black text-gray-50 md:text-xl lg:text-base" x-text="ratingValue(machine)"></p>
                    </div>
                </div>
            @endunless
        </div>
    </div>

    <div class="flex items-center justify-center">
        <p class="hidden px-3 text-xl font-black text-white md:block md:text-2xl lg:text-base">
            {{ $player['number'] }}
        </p>
    
        <p class="text-sm font-black text-white md:text-2xl lg:text-base">
            {{ $this->toLastName() }}
        </p>
    </div>
    
    @vite(['resources/css/player.css', 'resources/js/rating.js'])
</div>