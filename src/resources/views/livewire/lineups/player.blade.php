<x-util.modal-button>
    <x-slot:img>
        <div id="{{ $name }}" class="h-full {{ $size }}"
            :class=" componentName === 'startXI' ? 'invisible' : ''"
            x-data="{
                rating: @entangle('rating'),
                mom: @entangle('mom').live,
                machine: @entangle('defaultRating'),
                componentName: @entangle('name')
            }"
            wire:ignore.self>
            
            <div class="flex justify-center player">
                <div class="relative flex justify-center w-fit">
                    {{-- PlayerImage --}}
                    <x-player.player-image
                        class="{{ $size }} cursor-default"
                        :number="$player['number']"
                        :img="$player['img']" />

                    {{-- Goals --}}
                    <div class="absolute top-0 left-0 -translate-x-[60%]">
                        <x-player.goals
                            class="w-[13px] h-[13px] md:w-[14px] md:h-[14px]"
                            :goals="$player['goal']" />
                    </div>

                    {{-- Assists --}}
                    <div class="absolute top-0 right-0 translate-x-[60%]">
                        <x-player.assists
                            class="w-[13px] h-[13px] md:w-[14px] md:h-[14px]"
                            :assists="$player['assists']" />
                    </div>
                    
                    {{-- Rating --}}
                    <div class="absolute bottom-0 right-0 translate-x-[60%]">
                        @if ($isUser)
                            <x-player.rating :rating="$rating" :mom="$mom" :key="'user-rating'" />
                        @endif

                        @unless($isUser)
                            <x-player.rating :rating="$defaultRating" :key="'machine-rating'" />
                        @endunless
                    </div>
                </div>
            </div>

            <div class="flex items-center justify-center pointer-events-none gap-x-2">
                <p class="hidden text-sm font-black text-white md:block md:text-base">
                    {{ $player['number'] }}
                </p>
            
                <p class="text-sm font-black text-white md:text-base">
                    {{ $player['name'] }}
                </p>
            </div>
        </div>
    </x-slot:img>

    <x-slot:name></x-slot:name>
    
    <x-fixture.player-detail
        :$player 
        :$fixtureId />

    @vite(['resources/css/player.css', 'resources/js/rating.js'])
</x-util.modal-button>