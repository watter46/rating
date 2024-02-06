<div id="{{ $name }}" class="hidden h-full text-center {{ $size }}"
    x-data="{
        rating: @entangle('rating'),
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
            <div class="absolute top-0 left-0 -translate-x-1/3">
                <x-player.goals :goals="$player['goal']" />
            </div>

            {{-- Assists --}}
            <div class="absolute top-0 right-0 translate-x-1/3">
                <x-player.assists :assists="$player['assists']" />
            </div>
            
            {{-- Rating --}}
            <div class="text-sm font-black text-gray-50">
                @if ($isUser)
                    <div class="absolute bottom-0 right-0 w-2/3 translate-x-1/2">
                        @if($mom)
                            <div class="flex items-center justify-center px-3 py-0.5 gap-x-0.5 rounded-xl" style="background-color: #0E87E0">
                                <p class="text-xs">★</p>
                                <p x-text="ratingValue(rating)"></p>
                            </div>
                        @endif

                        @unless($mom)
                            <div class="flex justify-center px-3 py-0.5 rounded-xl"
                                :style="`background-color: ${ratingBgColor(rating)}`">
                                <p x-text="ratingValue(rating)"></p>
                            </div>
                        @endunless
                    </div>
                @endif

                @unless($isUser)
                    <div class="absolute bottom-0 right-0 w-2/3 translate-x-1/2">
                        <div class="flex justify-center px-3 py-0.5 rounded-xl"
                            :style="`background-color: ${ratingBgColor(machine)}`">
                            <p x-text="ratingValue(machine)"></p>
                        </div>
                    </div>
                @endunless
            </div>
        </div>
    </div>

    <div class="flex justify-center">
        {{-- <p class="text-xs">{{ $player['number'] }}</p> --}}
    
        <p class="text-sm font-black text-white">{{ $this->toLastName() }}</p>
    </div>
    
    @vite(['resources/css/player.css', 'resources/js/rating.js'])
</div>